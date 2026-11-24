<?php
namespace App\Models;

use App\Core\Model;
use RuntimeException;

class Appointment extends Model
{
    public function forPatient(int $patientId): array
    {
        $stmt = $this->db->prepare("SELECT a.*, s.TenDV, s.GiaDV, s.ThoiLuong, b.HoTen AS TenBacSi, l.NgayLamViec
            FROM LICH_HEN_KHAM a JOIN DICH_VU s ON s.MaDV=a.MaDV
            JOIN LICH_LAM_VIEC l ON l.MaLLV=a.MaLLV JOIN BAC_SI b ON b.MaBS=l.MaBS
            WHERE a.MaBN=? ORDER BY a.NgayKham DESC, a.GioBatDau DESC");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query("SELECT a.*, p.HoTen AS TenBenhNhan, p.SDT, s.TenDV, b.HoTen AS TenBacSi, l.NgayLamViec
            FROM LICH_HEN_KHAM a JOIN BENH_NHAN p ON p.MaBN=a.MaBN
            JOIN DICH_VU s ON s.MaDV=a.MaDV JOIN LICH_LAM_VIEC l ON l.MaLLV=a.MaLLV
            JOIN BAC_SI b ON b.MaBS=l.MaBS ORDER BY a.NgayKham DESC, a.GioBatDau DESC LIMIT 500")->fetchAll();
    }

    public function forDoctor(int $doctorId): array
    {
        $stmt = $this->db->prepare("SELECT a.*, p.HoTen AS TenBenhNhan, p.SDT, s.TenDV, l.NgayLamViec
            FROM LICH_HEN_KHAM a JOIN BENH_NHAN p ON p.MaBN=a.MaBN
            JOIN DICH_VU s ON s.MaDV=a.MaDV JOIN LICH_LAM_VIEC l ON l.MaLLV=a.MaLLV
            WHERE l.MaBS=? ORDER BY a.NgayKham DESC, a.GioBatDau DESC");
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll();
    }

    public function create(array $data, ?int $staffId): int
    {
        $this->db->beginTransaction();
        try {
            $lock = $this->db->prepare("SELECT l.*, b.MaBS FROM LICH_LAM_VIEC l JOIN BAC_SI b ON b.MaBS=l.MaBS WHERE l.MaLLV=? AND l.TrangThai='HOAT_DONG' FOR UPDATE");
            $lock->execute([(int)$data['MaLLV']]);
            $schedule = $lock->fetch();
            if (!$schedule) throw new RuntimeException('Lịch làm việc không tồn tại hoặc đã ngừng hoạt động.');
            if ($schedule['NgayLamViec'] !== $data['NgayKham']) throw new RuntimeException('Ngày khám không trùng ngày làm việc của ca đã chọn.');

            $serviceStmt = $this->db->prepare("SELECT * FROM DICH_VU WHERE MaDV=? AND TrangThai='DANG_AP_DUNG'");
            $serviceStmt->execute([(int)$data['MaDV']]);
            $service = $serviceStmt->fetch();
            if (!$service) throw new RuntimeException('Dịch vụ không tồn tại hoặc đã ngừng áp dụng.');

            $start = $data['GioBatDau'];
            $toMinutes = static function (string $time): int {
                $parts = explode(':', $time);
                if (count($parts) < 2 || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
                    throw new RuntimeException('Giờ bắt đầu không hợp lệ.');
                }
                return ((int)$parts[0] * 60) + (int)$parts[1];
            };
            $startMinutes = $toMinutes($start);
            $endMinutes = $startMinutes + ((int)$service['ThoiLuong']);
            $scheduleStart = $toMinutes($schedule['GioBatDau']);
            $scheduleEnd = $toMinutes($schedule['GioKetThuc']);
            if ($startMinutes < $scheduleStart || $endMinutes > $scheduleEnd || $endMinutes >= 24 * 60) {
                throw new RuntimeException('Khung giờ đặt nằm ngoài giờ làm việc của bác sĩ.');
            }
            $end = sprintf('%02d:%02d:00', intdiv($endMinutes, 60), $endMinutes % 60);
            if ($schedule['BatDauNghi'] !== null && $schedule['KetThucNghi'] !== null) {
                $breakStart = $toMinutes($schedule['BatDauNghi']);
                $breakEnd = $toMinutes($schedule['KetThucNghi']);
                if ($startMinutes < $breakEnd && $endMinutes > $breakStart) {
                    throw new RuntimeException('Khung giờ đặt trùng với giờ nghỉ của bác sĩ.');
                }
            }

            $conflict = $this->db->prepare("SELECT COUNT(*) FROM LICH_HEN_KHAM a JOIN LICH_LAM_VIEC l2 ON l2.MaLLV=a.MaLLV WHERE l2.MaBS=? AND a.NgayKham=? AND a.TrangThai NOT IN ('DA_HUY','HUY','CANCELLED') AND NOT (a.GioKetThuc <= ? OR a.GioBatDau >= ?)");
            $conflict->execute([(int)$schedule['MaBS'], $data['NgayKham'], $start, $end]);
            if ((int)$conflict->fetchColumn() > 0) throw new RuntimeException('Khung giờ này đã có lịch hẹn. Vui lòng chọn giờ khác.');

            $stmt = $this->db->prepare("INSERT INTO LICH_HEN_KHAM (MaBN, MaLLV, MaDV, MaNVTao, NgayKham, GioBatDau, GioKetThuc, TrangThai, GhiChu, NgayTao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([(int)$data['MaBN'], (int)$data['MaLLV'], (int)$data['MaDV'], $staffId, $data['NgayKham'], $start, $end, $staffId ? 'DA_XAC_NHAN' : 'CHO_XAC_NHAN', $data['GhiChu'] ?: null]);
            $id = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status): void
    {
        $allowed = ['CHO_XAC_NHAN', 'DA_XAC_NHAN', 'DA_HUY', 'DA_HOAN_THANH'];
        if (!in_array($status, $allowed, true)) throw new RuntimeException('Trạng thái không hợp lệ.');
        $stmt = $this->db->prepare('UPDATE LICH_HEN_KHAM SET TrangThai=? WHERE MaLH=?');
        $stmt->execute([$status, $id]);
    }

    public function findForDoctor(int $appointmentId, int $doctorId): ?array
    {
        $stmt = $this->db->prepare("SELECT a.*, p.MaBN, p.HoTen AS TenBenhNhan, l.MaBS, s.TenDV
            FROM LICH_HEN_KHAM a JOIN BENH_NHAN p ON p.MaBN=a.MaBN
            JOIN LICH_LAM_VIEC l ON l.MaLLV=a.MaLLV JOIN DICH_VU s ON s.MaDV=a.MaDV
            WHERE a.MaLH=? AND l.MaBS=? LIMIT 1");
        $stmt->execute([$appointmentId, $doctorId]);
        return $stmt->fetch() ?: null;
    }
}
