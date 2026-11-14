<?php
declare(strict_types=1);

/**
 * Tạo tài khoản mẫu cho bác sĩ, nhân viên và quản trị viên.
 * Chạy: D:\xamp\php\php.exe .\scripts\seed_internal_accounts.php
 * Script an toàn khi chạy nhiều lần: bỏ qua tên đăng nhập đã tồn tại.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Chi chay script nay tu Command Prompt/PowerShell.\n");
}

if (function_exists('sapi_windows_cp_set')) {
    @sapi_windows_cp_set(65001);
}

require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;
use App\Models\User;

$accounts = [
    [
        'TenDN' => 'bs01',
        'MatKhau' => '12345678',
        'VaiTro' => 'BAC_SI',
        'HoTen' => 'Bác sĩ Nguyễn Văn An',
        'SDT' => '0901000001',
        'Email' => 'bs01@example.com',
        'ChuyenMon' => 'Nha khoa tổng quát',
        'GioiThieu' => 'Bác sĩ phụ trách khám và điều trị nha khoa tổng quát.',
        'ChucVu' => null,
    ],
    [
        'TenDN' => 'nv01',
        'MatKhau' => '12345678',
        'VaiTro' => 'NHAN_VIEN',
        'HoTen' => 'Nhân viên Trần Thị Bình',
        'SDT' => '0901000002',
        'Email' => 'nv01@example.com',
        'ChuyenMon' => null,
        'GioiThieu' => null,
        'ChucVu' => 'Nhân viên tiếp nhận',
    ],
    [
        'TenDN' => 'admin01',
        'MatKhau' => '12345678',
        'VaiTro' => 'QUAN_TRI_VIEN',
        'HoTen' => null,
        'SDT' => null,
        'Email' => null,
        'ChuyenMon' => null,
        'GioiThieu' => null,
        'ChucVu' => null,
    ],
];

$roleLabels = ['BAC_SI' => 'Bác sĩ', 'NHAN_VIEN' => 'Nhân viên', 'QUAN_TRI_VIEN' => 'Quản trị viên'];

try {
    $db = Database::connection();
    $users = new User();
    $created = 0;
    $skipped = 0;

    foreach ($accounts as $account) {
        if ($users->usernameExists($account['TenDN'])) {
            echo "Bỏ qua: tài khoản '{$account['TenDN']}' đã tồn tại.\n";
            $skipped++;
            continue;
        }

        $db->beginTransaction();
        try {
            $userId = $users->create($account['TenDN'], $account['MatKhau'], $account['VaiTro']);

            if ($account['VaiTro'] === 'BAC_SI') {
                $stmt = $db->prepare('INSERT INTO BAC_SI (MaND, HoTen, SDT, Email, ChuyenMon, GioiThieu, TrangThai) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([
                    $userId, $account['HoTen'], $account['SDT'], $account['Email'],
                    $account['ChuyenMon'], $account['GioiThieu'], 'HOAT_DONG',
                ]);
            } elseif ($account['VaiTro'] === 'NHAN_VIEN') {
                $stmt = $db->prepare('INSERT INTO NHAN_VIEN (MaND, HoTen, SDT, Email, ChucVu, TrangThai) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([
                    $userId, $account['HoTen'], $account['SDT'], $account['Email'],
                    $account['ChucVu'], 'HOAT_DONG',
                ]);
            }

            $db->commit();
            echo "Đã tạo: {$account['TenDN']} ({$roleLabels[$account['VaiTro']]}), MaND = {$userId}.\n";
            $created++;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            echo "Lỗi khi tạo '{$account['TenDN']}': {$e->getMessage()}\n";
        }
    }

    echo "\nHoàn tất: {$created} tài khoản mới, {$skipped} tài khoản bỏ qua.\n\n";
    echo "THÔNG TIN ĐĂNG NHẬP MẪU\n";
    echo str_repeat('-', 62) . "\n";
    printf("%-12s %-14s %-16s\n", 'Tên đăng nhập', 'Mật khẩu', 'Vai trò');
    echo str_repeat('-', 62) . "\n";
    foreach ($accounts as $account) {
        printf("%-12s %-14s %-16s\n", $account['TenDN'], $account['MatKhau'], $roleLabels[$account['VaiTro']]);
    }
    echo str_repeat('-', 62) . "\n";
    echo "Hãy đổi mật khẩu trước khi dùng ngoài môi trường học tập/demo.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Khong the ket noi hoac khoi tao du lieu: {$e->getMessage()}\n");
    exit(1);
}
