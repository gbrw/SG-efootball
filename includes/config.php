<?php
// ─── Load .env ────────────────────────────────────────────────────────────────
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
        putenv(trim($key) . '=' . trim($val));
    }
}

// ─── Database — PostgreSQL (Supabase) ────────────────────────────────────────
// على Vercel: يُستخدم DATABASE_URL (Supabase Transaction Pooler).
// محلياً: يُستخدم DB_* من .env أو PostgreSQL محلي.
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? '5432');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'postgres');
define('DB_USER', $_ENV['DB_USER'] ?? 'postgres');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// ─── Supabase Storage ────────────────────────────────────────────────────────
define('SUPABASE_URL',              $_ENV['SUPABASE_URL']              ?? '');
define('SUPABASE_SERVICE_ROLE_KEY', $_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? '');
define('SUPABASE_BUCKET',           $_ENV['SUPABASE_BUCKET']           ?? 'uploads');

// ─── Site ─────────────────────────────────────────────────────────────────────
define('SITE_URL',     rtrim($_ENV['SITE_URL']  ?? 'https://your-project.vercel.app', '/'));
define('SITE_NAME',    $_ENV['SITE_NAME']       ?? 'Saif Jabbar');
define('CREATOR_NAME', $_ENV['CREATOR_NAME']    ?? 'Saif Jabbar');

// ─── Admin ───────────────────────────────────────────────────────────────────
define('LOCAL_AUTH',     filter_var($_ENV['LOCAL_AUTH']     ?? 'false', FILTER_VALIDATE_BOOLEAN));
define('ADMIN_EMAIL',    $_ENV['ADMIN_EMAIL']    ?? '');
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD'] ?? '');

// ─── App ─────────────────────────────────────────────────────────────────────
define('LOCALES',    ['ar', 'en']);
define('CATEGORIES', ['news', 'formations', 'upgrades', 'leaks']);

// ─── Session ─────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

require_once __DIR__ . '/db.php';
