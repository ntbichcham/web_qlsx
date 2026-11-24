<?php
namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException('Không tìm thấy giao diện: ' . $view);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        $pageTitle = $data['pageTitle'] ?? APP_NAME;
        require dirname(__DIR__) . '/Views/layouts/main.php';
    }

    protected function requireRole(array $roles): void
    {
        Auth::requireRole($roles);
    }

    protected function postOnly(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            render_error(405, 'Phương thức yêu cầu không được hỗ trợ.');
            exit;
        }
        Csrf::verify();
    }
}
