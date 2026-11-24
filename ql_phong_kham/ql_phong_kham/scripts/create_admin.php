<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Chi chay script nay tu Command Prompt/PowerShell.\n");
}

require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;

$username = trim((string)readline('Ten dang nhap quan tri: '));
$password = (string)readline('Mat khau (toi thieu 8 ky tu): ');
if ($username === '' || strlen($password) < 8) {
    fwrite(STDERR, "Ten dang nhap khong duoc rong; mat khau can it nhat 8 ky tu.\n");
    exit(1);
}

try {
    $db = Database::connection();
    $stmt = $db->prepare('INSERT INTO TAI_KHOAN_NGUOI_DUNG (TenDN, MatKhau, VaiTro, TrangThai) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), 'QUAN_TRI_VIEN', 'HOAT_DONG']);
    echo "Da tao tai khoan quan tri. MaND: " . $db->lastInsertId() . "\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Khong the tao tai khoan. Kiem tra database va ten dang nhap co bi trung khong.\n");
    exit(1);
}
