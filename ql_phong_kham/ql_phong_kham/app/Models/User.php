<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function findForLogin(string $username): ?array
    {
        $sql = "SELECT u.*, p.MaBN, p.HoTen AS PatientName, d.MaBS, d.HoTen AS DoctorName,
                       s.MaNV, s.HoTen AS StaffName
                FROM TAI_KHOAN_NGUOI_DUNG u
                LEFT JOIN BENH_NHAN p ON p.MaND = u.MaND
                LEFT JOIN BAC_SI d ON d.MaND = u.MaND
                LEFT JOIN NHAN_VIEN s ON s.MaND = u.MaND
                WHERE u.TenDN = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $row['HoTen'] = $row['PatientName'] ?: ($row['DoctorName'] ?: ($row['StaffName'] ?: $row['TenDN']));
        return $row;
    }

    public function create(string $username, string $password, string $role): int
    {
        $stmt = $this->db->prepare('INSERT INTO TAI_KHOAN_NGUOI_DUNG (TenDN, MatKhau, VaiTro, TrangThai) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $role, 'HOAT_DONG']);
        return (int)$this->db->lastInsertId();
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM TAI_KHOAN_NGUOI_DUNG WHERE TenDN = ? LIMIT 1');
        $stmt->execute([$username]);
        return (bool)$stmt->fetchColumn();
    }

    public function all(): array
    {
        $sql = "SELECT u.MaND, u.TenDN, u.VaiTro, u.TrangThai, u.NgayTao,
                       COALESCE(d.HoTen, s.HoTen, p.HoTen) AS HoTen
                FROM TAI_KHOAN_NGUOI_DUNG u
                LEFT JOIN BAC_SI d ON d.MaND = u.MaND
                LEFT JOIN NHAN_VIEN s ON s.MaND = u.MaND
                LEFT JOIN BENH_NHAN p ON p.MaND = u.MaND
                ORDER BY u.MaND DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE TAI_KHOAN_NGUOI_DUNG SET TrangThai = ? WHERE MaND = ?');
        $stmt->execute([$status, $id]);
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT u.MaND, u.TenDN, u.VaiTro, u.TrangThai, u.NgayTao,
                       d.MaBS, d.HoTen AS TenBS, d.SDT AS SDTBS, d.Email AS EmailBS,
                       d.ChuyenMon, d.GioiThieu,
                       s.MaNV, s.HoTen AS TenNV, s.SDT AS SDTNV, s.Email AS EmailNV, s.ChucVu,
                       p.MaBN, p.HoTen AS TenBN
                FROM TAI_KHOAN_NGUOI_DUNG u
                LEFT JOIN BAC_SI d ON d.MaND = u.MaND
                LEFT JOIN NHAN_VIEN s ON s.MaND = u.MaND
                LEFT JOIN BENH_NHAN p ON p.MaND = u.MaND
                WHERE u.MaND = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function updatePassword(int $id, string $password): void
    {
        $stmt = $this->db->prepare('UPDATE TAI_KHOAN_NGUOI_DUNG SET MatKhau = ? WHERE MaND = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function updateDoctorProfile(int $doctorId, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE BAC_SI SET HoTen=?, SDT=?, Email=?, ChuyenMon=?, GioiThieu=? WHERE MaBS=?');
        $stmt->execute([
            $data['HoTen'],
            $data['SDT'] ?: null,
            $data['Email'] ?: null,
            $data['ChuyenMon'] ?: null,
            $data['GioiThieu'] ?: null,
            $doctorId,
        ]);
    }

    public function updateStaffProfile(int $staffId, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE NHAN_VIEN SET HoTen=?, SDT=?, Email=?, ChucVu=? WHERE MaNV=?');
        $stmt->execute([
            $data['HoTen'],
            $data['SDT'] ?: null,
            $data['Email'] ?: null,
            $data['ChucVu'] ?: null,
            $staffId,
        ]);
    }
}
