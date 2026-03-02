<?php
/**
 * PDO helper — يدعم MySQL (محلي) و PostgreSQL (Railway / Supabase)
 * إذا كان DATABASE_URL موجوداً يستخدم PostgreSQL، وإلا MySQL.
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

    $dbUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: null;

    if ($dbUrl) {
        // PostgreSQL via DATABASE_URL (Railway / Supabase)
        $p    = parse_url($dbUrl);
        $host = $p['host'];
        $port = $p['port'] ?? 5432;
        $name = ltrim($p['path'], '/');
        $user = $p['user'];
        $pass = $p['pass'] ?? '';
        $dsn  = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
    } else {
        // MySQL for local development
        $dsn  = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4;port=' . DB_PORT;
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

/**
 * Returns true when connected to PostgreSQL (Railway/Supabase).
 */
function isPostgres(): bool
{
    return db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
}
