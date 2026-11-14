<?php
namespace App\Models;

use App\Core\Model;
use RuntimeException;

class Schedule extends Model
{
    private const TIME_PATTERN = '/^([01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/';

    public function all(): array
    {
        return $this->db->query("SELECT l.*, b.HoTen AS TenBacSi, b.ChuyenMon FROM LICH_LAM_VIEC l JOIN BAC_SI b ON b.MaBS=l.MaBS ORDER BY l.NgayLamViec DESC, l.GioBatDau LIMIT 300")->fetchAll();
    }

    public function upcoming(): array
    {
        $stmt = $this->db->prepare("SELECT l.*, b.HoTen AS TenBacSi, b.ChuyenMon FROM LICH_LAM_VIEC l JOIN BAC_SI b ON b.MaBS=l.MaBS WHERE l.NgayLamViec >= CURDATE() AND l.TrangThai='HOAT_DONG' ORDER BY l.NgayLamViec, l.GioBatDau LIMIT 100");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO LICH_LAM_VIEC (MaBS, NgayLamViec, GioBatDau, GioKetThuc, BatDauNghi, KetThucNghi, TrangThai) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([(int)$data['MaBS'], $data['NgayLamViec'], $data['GioBatDau'], $data['GioKetThuc'], $data['BatDauNghi'] ?: null, $data['KetThucNghi'] ?: null, $data['TrangThai']]);
    }

    /**
     * Danh sách ca làm việc của một bác sĩ kèm số lịch hẹn còn hiệu lực.
     */
    public function forDoctor(int $doctorId): array
    {
        $sql = "SELECT l.*,
                       (SELECT COUNT(*) FROM LICH_HEN_KHAM a
                        WHERE a.MaLLV = l.MaLLV AND a.TrangThai NOT IN ('DA_HUY','HUY','CANCELLED')) AS SoLichHen
                FROM LICH_LAM_VIEC l
                WHERE l.MaBS = ?
                ORDER BY l.NgayLamViec DESC, l.GioBatDau DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll();
    }

    public function findForDoctor(int $scheduleId, int $doctorId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM LICH_LAM_VIEC WHERE MaLLV = ? AND MaBS = ? LIMIT 1');
        $stmt->execute([$scheduleId, $doctorId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Bác sĩ tự đăng ký ca làm việc. Ca có hiệu lực ngay (HOAT_DONG).
     */
    public function createForDoctor(int $doctorId, array $data): int
    {
        $date = trim((string)($data['NgayLamViec'] ?? ''));
        $start = trim((string)($data['GioBatDau'] ?? ''));
        $end = trim((string)($data['GioKetThuc'] ?? ''));
        $breakStart = trim((string)($data['BatDauNghi'] ?? ''));
        $breakEnd = trim((string)($data['KetThucNghi'] ?? ''));

        if (!$this->isValidDate($date)) {
            throw new RuntimeException('Ngày làm việc không hợp lệ.');
        }
        if ($date < date('Y-m-d')) {
            throw new RuntimeException('Không thể đăng ký ca làm việc cho ngày đã qua.');
        }
        if (!preg_match(self::TIME_PATTERN, $start) || !preg_match(self::TIME_PATTERN, $end)) {
            throw new RuntimeException('Giờ bắt đầu và giờ kết thúc không hợp lệ.');
        }

        $startMinutes = $this->toMinutes($start);
        $endMinutes = $this->toMinutes($end);
        if ($startMinutes >= $endMinutes) {
            throw new RuntimeException('Giờ kết thúc phải sau giờ bắt đầu.');
        }

        $hasBreakStart = $breakStart !== '';
        $hasBreakEnd = $breakEnd !== '';
        if ($hasBreakStart !== $hasBreakEnd) {
            throw new RuntimeException('Vui lòng nhập đầy đủ cả giờ bắt đầu và kết thúc nghỉ, hoặc để trống cả hai.');
        }

        $breakStartMinutes = null;
        $breakEndMinutes = null;
        if ($hasBreakStart) {
            if (!preg_match(self::TIME_PATTERN, $breakStart) || !preg_match(self::TIME_PATTERN, $breakEnd)) {
                throw new RuntimeException('Giờ nghỉ không hợp lệ.');
            }
            $breakStartMinutes = $this->toMinutes($breakStart);
            $breakEndMinutes = $this->toMinutes($breakEnd);
            if ($breakStartMinutes >= $breakEndMinutes) {
                throw new RuntimeException('Giờ kết thúc nghỉ phải sau giờ bắt đầu nghỉ.');
            }
            if ($breakStartMinutes <= $startMinutes || $breakEndMinutes >= $endMinutes) {
                throw new RuntimeException('Giờ nghỉ phải nằm trong khoảng giờ làm việc.');
            }
        }

        $this->db->beginTransaction();
        try {
            $lock = $this->db->prepare("SELECT MaLLV, GioBatDau, GioKetThuc FROM LICH_LAM_VIEC
                WHERE MaBS = ? AND NgayLamViec = ? AND TrangThai = 'HOAT_DONG' FOR UPDATE");
            $lock->execute([$doctorId, $date]);
            foreach ($lock->fetchAll() as $existing) {
                $existingStart = $this->toMinutes((string)$existing['GioBatDau']);
                $existingEnd = $this->toMinutes((string)$existing['GioKetThuc']);
                if ($startMinutes < $existingEnd && $endMinutes > $existingStart) {
                    throw new RuntimeException('Ca làm việc bị trùng giờ với ca #' . (int)$existing['MaLLV'] . ' trong ngày. Vui lòng chọn khung giờ khác.');
                }
            }

            $stmt = $this->db->prepare('INSERT INTO LICH_LAM_VIEC (MaBS, NgayLamViec, GioBatDau, GioKetThuc, BatDauNghi, KetThucNghi, TrangThai) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $doctorId,
                $date,
                $this->normalizeTime($start),
                $this->normalizeTime($end),
                $breakStart !== '' ? $this->normalizeTime($breakStart) : null,
                $breakEnd !== '' ? $this->normalizeTime($breakEnd) : null,
                'HOAT_DONG',
            ]);
            $id = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Bác sĩ ngừng một ca của chính mình nếu ca chưa có lịch hẹn còn hiệu lực.
     */
    public function cancelForDoctor(int $scheduleId, int $doctorId): void
    {
        $schedule = $this->findForDoctor($scheduleId, $doctorId);
        if (!$schedule) {
            throw new RuntimeException('Không tìm thấy ca làm việc thuộc quyền quản lý của bạn.');
        }
        if ($schedule['TrangThai'] !== 'HOAT_DONG') {
            throw new RuntimeException('Ca làm việc này đã ngừng hoạt động.');
        }
        if ($schedule['NgayLamViec'] < date('Y-m-d')) {
            throw new RuntimeException('Ca làm việc đã diễn ra, không thể ngừng.');
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM LICH_HEN_KHAM WHERE MaLLV = ? AND TrangThai NOT IN ('DA_HUY','HUY','CANCELLED')");
        $stmt->execute([$scheduleId]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new RuntimeException('Ca làm việc đã có lịch hẹn. Vui lòng xử lý hoặc hủy các lịch hẹn trước khi ngừng ca.');
        }

        $update = $this->db->prepare("UPDATE LICH_LAM_VIEC SET TrangThai = 'NGUNG_HOAT_DONG' WHERE MaLLV = ? AND MaBS = ?");
        $update->execute([$scheduleId, $doctorId]);
    }

    /**
     * Quản trị viên bật/tắt một ca làm việc.
     */
    public function updateStatusByAdmin(int $scheduleId, string $status): void
    {
        if (!in_array($status, ['HOAT_DONG', 'NGUNG_HOAT_DONG'], true)) {
            throw new RuntimeException('Trạng thái ca làm việc không hợp lệ.');
        }
        if ($status === 'NGUNG_HOAT_DONG') {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM LICH_HEN_KHAM WHERE MaLLV = ? AND TrangThai NOT IN ('DA_HUY','HUY','CANCELLED')");
            $stmt->execute([$scheduleId]);
            if ((int)$stmt->fetchColumn() > 0) {
                throw new RuntimeException('Ca làm việc đã có lịch hẹn, không thể ngừng.');
            }
        }
        $update = $this->db->prepare('UPDATE LICH_LAM_VIEC SET TrangThai = ? WHERE MaLLV = ?');
        $update->execute([$status, $scheduleId]);
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function toMinutes(string $time): int
    {
        $parts = explode(':', $time);
        return ((int)$parts[0] * 60) + (int)$parts[1];
    }

    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? $time . ':00' : $time;
    }
}
