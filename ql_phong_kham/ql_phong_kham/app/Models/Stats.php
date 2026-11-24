<?php
namespace App\Models;

use App\Core\Model;

class Stats extends Model
{
    public function counts(): array
    {
        $tables = ['BENH_NHAN', 'BAC_SI', 'NHAN_VIEN', 'DICH_VU', 'LICH_HEN_KHAM'];
        $counts = [];
        foreach ($tables as $table) {
            $counts[$table] = (int)$this->db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        }
        return $counts;
    }
}
