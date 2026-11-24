<?php
namespace App\Core;

final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'MaND' => (int)$user['MaND'],
            'TenDN' => $user['TenDN'],
            'VaiTro' => $user['VaiTro'],
            'TrangThai' => $user['TrangThai'],
            'MaBN' => isset($user['MaBN']) ? (int)$user['MaBN'] : null,
            'MaBS' => isset($user['MaBS']) ? (int)$user['MaBS'] : null,
            'MaNV' => isset($user['MaNV']) ? (int)$user['MaNV'] : null,
            'HoTen' => $user['HoTen'] ?? $user['TenDN'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('warning', 'Vui lòng đăng nhập để tiếp tục.');
            redirect_to('auth.login');
        }
    }

    public static function requireRole(array $roles): void
    {
        self::requireLogin();
        $role = self::user()['VaiTro'] ?? '';
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            render_error(403, 'Bạn không có quyền truy cập chức năng này.');
            exit;
        }
    }

    public static function homeRoute(): string
    {
        return match (self::user()['VaiTro'] ?? '') {
            'BENH_NHAN' => 'patient.dashboard',
            'BAC_SI' => 'doctor.dashboard',
            'NHAN_VIEN' => 'staff.dashboard',
            'QUAN_TRI_VIEN' => 'admin.dashboard',
            default => 'home',
        };
    }
}
