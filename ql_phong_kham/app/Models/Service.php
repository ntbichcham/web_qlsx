<?php
namespace App\Models;

use App\Core\Model;

class Service extends Model
{
    public function all(bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM DICH_VU';
        if ($activeOnly) $sql .= " WHERE TrangThai='DANG_AP_DUNG'";
        $sql .= ' ORDER BY TenDV';
        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO DICH_VU (TenDV, MoTa, ThoiLuong, GiaDV, TrangThai) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$data['TenDV'], $data['MoTa'] ?: null, (int)$data['ThoiLuong'], (float)$data['GiaDV'], $data['TrangThai']]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE DICH_VU SET TrangThai=? WHERE MaDV=?');
        $stmt->execute([$status, $id]);
    }
}
