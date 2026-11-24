<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Tự xác định đường dẫn ứng dụng khi đặt thư mục trong XAMPP/htdocs.
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/ql_phong_kham/public/index.php'));
define('APP_URL', rtrim($scriptDir, '/'));
define('APP_NAME', 'Phòng khám nha khoa');
define('APP_ENV', 'development');
define('APP_DEBUG', false);

