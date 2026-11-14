<?php
namespace App\Models;

use App\Core\Model;

class Staff extends Model
{
    public function findByUser(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM NHAN_VIEN WHERE MaND=?');
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }
}
