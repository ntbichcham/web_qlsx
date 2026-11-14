<?php
namespace App\Models;

use App\Core\Model;

class Patient extends Model
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM BENH_NHAN WHERE MaBN = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUser(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM BENH_NHAN WHERE MaND = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function all(string $keyword = ''): array
    {
        $sql = 'SELECT MaBN, MaND, HoTen, SDT, Email, GioiTinh, NgaySinh FROM BENH_NHAN';
        $params = [];
        if ($keyword !== '') {
            $sql .= ' WHERE HoTen LIKE ? OR SDT LIKE ? OR Email LIKE ?';
            $like = '%' . $keyword . '%';
            $params = [$like, $like, $like];
        }
        $sql .= ' ORDER BY MaBN DESC LIMIT 300';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data, ?int $userId = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO BENH_NHAN (MaND, HoTen, SDT, Email, DiaChi, GioiTinh, NgaySinh) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $data['HoTen'], $data['SDT'] ?: null, $data['Email'] ?: null, $data['DiaChi'] ?: null, $data['GioiTinh'] ?: null, $data['NgaySinh'] ?: null]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE BENH_NHAN SET HoTen=?, SDT=?, Email=?, DiaChi=?, GioiTinh=?, NgaySinh=? WHERE MaBN=?');
        $stmt->execute([$data['HoTen'], $data['SDT'] ?: null, $data['Email'] ?: null, $data['DiaChi'] ?: null, $data['GioiTinh'] ?: null, $data['NgaySinh'] ?: null, $id]);
    }

    public function createMedicalRecordIfMissing(int $patientId): int
    {
        $stmt = $this->db->prepare('SELECT MaHSBA FROM HO_SO_BENH_AN WHERE MaBN=?');
        $stmt->execute([$patientId]);
        $id = $stmt->fetchColumn();
        if ($id) return (int)$id;
        $insert = $this->db->prepare("INSERT INTO HO_SO_BENH_AN (MaBN, NgayLap, TrangThai) VALUES (?, NOW(), 'DANG_THEO_DOI')");
        $insert->execute([$patientId]);
        return (int)$this->db->lastInsertId();
    }
}
