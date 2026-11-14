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
}
