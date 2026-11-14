<?php
namespace App\Models;

use App\Core\Model;
use RuntimeException;

class Clinical extends Model
{
    public function patientHistory(int $patientId): array
    {
        $stmt = $this->db->prepare("SELECT v.MaLKham, v.NgayGioKham, v.LyDoKham, v.TrieuChung, v.ChanDoan,
                v.KetQuaKham, v.HuongXuLy, v.TrangThaiLanKham, d.LyDoDieuTri, doc.HoTen AS TenBacSi,
                dt.MaDonThuoc, dt.LoiDan AS LoiDanThuoc,
                (SELECT GROUP_CONCAT(ct.TenThuoc SEPARATOR ', ') FROM CHI_TIET_DON_THUOC ct WHERE ct.MaDonThuoc=dt.MaDonThuoc) AS DanhSachThuoc
            FROM HO_SO_BENH_AN h JOIN DOT_DIEU_TRI d ON d.MaHSBA=h.MaHSBA
            JOIN LAN_KHAM v ON v.MaDT=d.MaDT JOIN BAC_SI doc ON doc.MaBS=v.MaBS
            LEFT JOIN DON_THUOC dt ON dt.MaLKham=v.MaLKham
            WHERE h.MaBN=? ORDER BY v.NgayGioKham DESC");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function treatmentsForDoctor(int $doctorId): array
    {
        $stmt = $this->db->prepare("SELECT d.*, h.MaBN, p.HoTen AS TenBenhNhan
            FROM DOT_DIEU_TRI d JOIN HO_SO_BENH_AN h ON h.MaHSBA=d.MaHSBA
            JOIN BENH_NHAN p ON p.MaBN=h.MaBN WHERE d.MaBS=? ORDER BY d.NgayBatDau DESC");
        $stmt->execute([$doctorId]);
        return $stmt->fetchAll();
    }

    public function createVisitFromAppointment(array $appointment, int $doctorId): int
    {
        $this->db->beginTransaction();
        try {
            $record = $this->db->prepare('SELECT MaHSBA FROM HO_SO_BENH_AN WHERE MaBN=?');
            $record->execute([(int)$appointment['MaBN']]);
            $medicalRecordId = $record->fetchColumn();
            if (!$medicalRecordId) {
                $insertRecord = $this->db->prepare("INSERT INTO HO_SO_BENH_AN (MaBN, NgayLap, TrangThai) VALUES (?, NOW(), 'DANG_THEO_DOI')");
                $insertRecord->execute([(int)$appointment['MaBN']]);
                $medicalRecordId = (int)$this->db->lastInsertId();
            }

            $existing = $this->db->prepare('SELECT MaLKham FROM LAN_KHAM WHERE MaLH=?');
            $existing->execute([(int)$appointment['MaLH']]);
            $visitId = $existing->fetchColumn();
            if ($visitId) {
                $this->db->commit();
                return (int)$visitId;
            }

            $treatment = $this->db->prepare("INSERT INTO DOT_DIEU_TRI (MaHSBA, MaBS, LyDoDieuTri, NgayBatDau, TrangThai) VALUES (?, ?, ?, CURDATE(), 'DANG_DIEU_TRI')");
            $treatment->execute([(int)$medicalRecordId, $doctorId, 'Điều trị từ lịch hẹn #' . $appointment['MaLH']]);
            $treatmentId = (int)$this->db->lastInsertId();

            $visit = $this->db->prepare("INSERT INTO LAN_KHAM (MaDT, MaBS, MaLH, NgayGioKham, LyDoKham, TrangThaiLanKham, NgayTao) VALUES (?, ?, ?, NOW(), ?, 'DA_TIEP_NHAN', NOW())");
            $visit->execute([$treatmentId, $doctorId, (int)$appointment['MaLH'], $appointment['GhiChu'] ?: 'Khám theo lịch hẹn']);
            $visitId = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $visitId;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    public function visitForDoctor(int $visitId, int $doctorId): ?array
    {
        $stmt = $this->db->prepare("SELECT v.*, p.MaBN, p.HoTen AS TenBenhNhan, d.MaDT
            FROM LAN_KHAM v JOIN DOT_DIEU_TRI d ON d.MaDT=v.MaDT
            JOIN HO_SO_BENH_AN h ON h.MaHSBA=d.MaHSBA JOIN BENH_NHAN p ON p.MaBN=h.MaBN
            WHERE v.MaLKham=? AND v.MaBS=? AND d.MaBS=? LIMIT 1");
        $stmt->execute([$visitId, $doctorId, $doctorId]);
        return $stmt->fetch() ?: null;
    }

    public function updateVisit(int $visitId, int $doctorId, array $data): void
    {
        if (!$this->visitForDoctor($visitId, $doctorId)) throw new RuntimeException('Không tìm thấy lần khám trong phạm vi được phân công.');
        $stmt = $this->db->prepare('UPDATE LAN_KHAM SET LyDoKham=?, TrieuChung=?, ChanDoan=?, KetQuaKham=?, HuongXuLy=?, GhiChu=?, TrangThaiLanKham=? WHERE MaLKham=?');
        $stmt->execute([$data['LyDoKham'] ?: null, $data['TrieuChung'] ?: null, $data['ChanDoan'] ?: null, $data['KetQuaKham'] ?: null, $data['HuongXuLy'] ?: null, $data['GhiChu'] ?: null, $data['TrangThaiLanKham'], $visitId]);
    }

    public function addVisitService(int $visitId, int $doctorId, array $data): void
    {
        if (!$this->visitForDoctor($visitId, $doctorId)) throw new RuntimeException('Không có quyền cập nhật lần khám này.');
        $stmt = $this->db->prepare('INSERT INTO DICH_VU_LAN_KHAM (MaLKham, MaDV, SoLuong, DonGiaApDung, ThoiGianThucHien, GhiChu) SELECT ?, MaDV, ?, GiaDV, NOW(), ? FROM DICH_VU WHERE MaDV=? AND TrangThai=\'DANG_AP_DUNG\'');
        $stmt->execute([$visitId, (int)$data['SoLuong'], $data['GhiChu'] ?: null, (int)$data['MaDV']]);
        if ($stmt->rowCount() === 0) throw new RuntimeException('Dịch vụ không hợp lệ hoặc đã ngừng áp dụng.');
    }

    public function prescriptionForVisit(int $visitId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM DON_THUOC WHERE MaLKham=?');
        $stmt->execute([$visitId]);
        return $stmt->fetch() ?: null;
    }

    public function createPrescription(int $visitId, int $doctorId, string $advice): int
    {
        if (!$this->visitForDoctor($visitId, $doctorId)) throw new RuntimeException('Không có quyền kê đơn cho lần khám này.');
        $existing = $this->prescriptionForVisit($visitId);
        if ($existing) return (int)$existing['MaDonThuoc'];
        $stmt = $this->db->prepare('INSERT INTO DON_THUOC (MaLKham, MaBS, NgayKeDon, LoiDan) VALUES (?, ?, CURDATE(), ?)');
        $stmt->execute([$visitId, $doctorId, $advice ?: null]);
        return (int)$this->db->lastInsertId();
    }

    public function addMedicine(int $prescriptionId, int $doctorId, array $data): void
    {
        $stmt = $this->db->prepare('SELECT d.MaDonThuoc FROM DON_THUOC d JOIN LAN_KHAM v ON v.MaLKham=d.MaLKham WHERE d.MaDonThuoc=? AND d.MaBS=? AND v.MaBS=?');
        $stmt->execute([$prescriptionId, $doctorId, $doctorId]);
        if (!$stmt->fetch()) throw new RuntimeException('Không có quyền chỉnh sửa đơn thuốc này.');
        $insert = $this->db->prepare('INSERT INTO CHI_TIET_DON_THUOC (MaDonThuoc, TenThuoc, SoLuong, LieuLuong, CachDung, ThoiGianBatDau, ThoiGianKetThuc) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$prescriptionId, $data['TenThuoc'], (float)$data['SoLuong'], $data['LieuLuong'] ?: null, $data['CachDung'] ?: null, ($data['ThoiGianBatDau'] ?: null), ($data['ThoiGianKetThuc'] ?: null)]);
    }

    public function medicineRows(int $prescriptionId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM CHI_TIET_DON_THUOC WHERE MaDonThuoc=? ORDER BY MaCTDT');
        $stmt->execute([$prescriptionId]);
        return $stmt->fetchAll();
    }
}
