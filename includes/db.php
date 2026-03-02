<?php
/**
 * PDO helper — MySQL
 * على Railway: يستخدم MYSQL_URL تلقائياً عند إضافة MySQL plugin
 * محلياً: يستخدم DB_* من .env
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 1) MYSQL_URL or MYSQL_PUBLIC_URL (Railway MySQL service vars)
    $mysqlUrl = $_ENV['MYSQL_URL']        ?? getenv('MYSQL_URL')        ?:
               ($_ENV['MYSQL_PUBLIC_URL'] ?? getenv('MYSQL_PUBLIC_URL') ?: null);

    if ($mysqlUrl) {
        $p    = parse_url($mysqlUrl);
        $host = $p['host'];
        $port = $p['port'] ?? 3306;
        $name = ltrim($p['path'], '/');
        $user = rawurldecode($p['user']);
        $pass = rawurldecode($p['pass'] ?? '');
        $dsn  = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    } elseif (!empty($_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST'))) {
        // 2) Railway individual MySQL env vars (MYSQLHOST, MYSQLPASSWORD, etc.)
        $host = $_ENV['MYSQLHOST']     ?? getenv('MYSQLHOST');
        $port = $_ENV['MYSQLPORT']     ?? getenv('MYSQLPORT') ?: 3306;
        $name = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?: (
                $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE') ?: 'railway');
        $user = $_ENV['MYSQLUSER']     ?? getenv('MYSQLUSER') ?: 'root';
        $pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?:
                ($_ENV['MYSQL_ROOT_PASSWORD'] ?? getenv('MYSQL_ROOT_PASSWORD') ?: '');
        $dsn  = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    } else {
        // 3) Local development via DB_* variables
        $dsn  = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $user = DB_USER;
        $pass = DB_PASS;
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, $opts);
    } catch (PDOException $e) {
        throw new RuntimeException('DB connection failed: ' . $e->getMessage());
    }

    return $pdo;
}
