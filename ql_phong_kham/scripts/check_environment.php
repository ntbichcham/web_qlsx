<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;

echo "PHP: " . PHP_VERSION . PHP_EOL;
echo "PDO: " . (extension_loaded('pdo') ? 'OK' : 'MISSING') . PHP_EOL;
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? 'OK' : 'MISSING') . PHP_EOL;
echo "mbstring: " . (extension_loaded('mbstring') ? 'OK' : 'MISSING') . PHP_EOL;
try {
    $db = Database::connection();
    echo "MariaDB connection: OK" . PHP_EOL;
    $count = (int)$db->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchColumn();
    echo "Tables in selected database: {$count}" . PHP_EOL;
} catch (Throwable $e) {
    echo "MariaDB connection: FAILED" . PHP_EOL;
    echo "Check config/database.php and start MySQL in XAMPP Control Panel." . PHP_EOL;
    exit(1);
}
