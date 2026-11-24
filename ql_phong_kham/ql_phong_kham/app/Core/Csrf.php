<?php
namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verify(): void
    {
        $submitted = (string)($_POST['_csrf'] ?? '');
        $known = (string)($_SESSION['_csrf'] ?? '');
        if ($known === '' || $submitted === '' || !hash_equals($known, $submitted)) {
            http_response_code(419);
            render_error(419, 'Phiên biểu mẫu đã hết hạn. Vui lòng tải lại trang và thử lại.');
            exit;
        }
    }
}
