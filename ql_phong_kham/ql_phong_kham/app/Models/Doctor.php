<?php
namespace App\Models;

use App\Core\Model;

class Doctor extends Model
{
    public function all(): array
    {
        return $this->db->query("SELECT d.MaBS, d.HoTen, d.SDT, d.Email, d.ChuyenMon, d.TrangThai FROM BAC_SI d ORDER BY d.HoTen")->fetchAll();
    }

    public function findByUser(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM BAC_SI WHERE MaND=?');
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }
}
