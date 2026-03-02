<?php
/**
 * PDO helper — PostgreSQL (Supabase)
 * على Vercel: يستخدم DATABASE_URL (Supabase Transaction Pooler — موصى به للـ serverless)
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

    // 1) DATABASE_URL — Supabase Transaction Pooler (Vercel)
    $dbUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: null;

    if ($dbUrl) {
        $p = parse_url($dbUrl);
        if (empty($p['host']) || str_contains($p['host'], '[') || str_contains($p['host'], 'REGION')) {
            throw new RuntimeException(
                'DATABASE_URL غير صحيح — تأكد من استبدال [REGION] بالمنطقة الحقيقية من Supabase Dashboard → Settings → Database → Connection Pooling'
            );
        }
        $host = $p['host'];
        $port = $p['port'] ?? 5432;
        $name = ltrim($p['path'], '/');
        $user = rawurldecode($p['user']);
        $pass = rawurldecode($p['pass'] ?? '');
        $dsn  = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
    } else {
        // 2) Local development via DB_* variables
        $dsn  = 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';sslmode=prefer';
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
