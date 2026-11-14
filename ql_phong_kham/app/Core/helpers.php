<?php
use App\Core\Auth;
use App\Core\Csrf;

function e(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $route = 'home', array $params = []): string
{
    $query = array_merge(['r' => $route], $params);
    return APP_URL . '/?' . http_build_query($query);
}

function asset(string $path): string
{
    return APP_URL . '/assets/' . ltrim($path, '/');
}

function redirect_to(string $route, array $params = [])
{
    header('Location: ' . url($route, $params));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function pull_flashes(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
}

function current_user(): ?array
{
    return Auth::user();
}

function money(mixed $amount): string
{
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}

function status_badge(string $status): string
{
    $map = [
        'CHO_XAC_NHAN' => 'warning', 'DA_XAC_NHAN' => 'primary',
        'DA_HOAN_THANH' => 'success', 'DA_HUY' => 'secondary',
        'DANG_DIEU_TRI' => 'info', 'HOAT_DONG' => 'success', 'KHOA' => 'danger',
        'NGUNG_HOAT_DONG' => 'secondary', 'DANG_AP_DUNG' => 'success', 'NGUNG_AP_DUNG' => 'secondary',
    ];
    $labels = [
        'CHO_XAC_NHAN' => 'Chờ xác nhận', 'DA_XAC_NHAN' => 'Đã xác nhận',
        'DA_HOAN_THANH' => 'Đã hoàn thành', 'DA_HUY' => 'Đã hủy',
        'DANG_DIEU_TRI' => 'Đang điều trị', 'HOAT_DONG' => 'Hoạt động', 'KHOA' => 'Đã khóa',
        'NGUNG_HOAT_DONG' => 'Ngừng hoạt động', 'DANG_AP_DUNG' => 'Đang áp dụng',
        'NGUNG_AP_DUNG' => 'Ngừng áp dụng',
    ];
    $label = $labels[$status] ?? str_replace('_', ' ', $status);
    return '<span class="badge text-bg-' . ($map[$status] ?? 'secondary') . '">' . e($label) . '</span>';
}

function render_error(int $code, string $message): void
{
    $pageTitle = 'Lỗi ' . $code;
    $content = '<div class="container py-5"><div class="alert alert-warning"><h1 class="h4">Lỗi ' . $code . '</h1><p class="mb-0">' . e($message) . '</p></div><a class="btn btn-outline-primary" href="' . e(url('home')) . '">Về trang chủ</a></div>';
    require __DIR__ . '/../Views/layouts/main.php';
}
