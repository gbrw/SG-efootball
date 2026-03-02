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

// ─── Database ─────────────────────────────────────────────────────────────────
// على Railway يُستخدم DATABASE_URL مباشرةً (يُضبط تلقائياً).
// محلياً يُستخدم MySQL عبر المتغيرات أدناه.
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'sg_efootball');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// ─── Site ─────────────────────────────────────────────────────────────────────
define('SITE_URL',     rtrim($_ENV['SITE_URL']  ?? 'http://efb.local', '/'));
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
