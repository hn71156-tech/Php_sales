<?php
declare(strict_types=1);

define('APP_NAME', 'StockPro');
define('BASE_URL', '/php_inventory_sales/public');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'stockpro_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP mặc định thường để trống; hãy đổi nếu MySQL của bạn có mật khẩu.
define('APP_DEBUG', true); // Đặt false khi triển khai thật.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}
