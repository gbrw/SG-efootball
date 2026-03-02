<?php
require_once __DIR__ . '/config.php';

/**
 * Cookie-based auth — يعمل على Vercel serverless (لا يحتاج file sessions)
 * AUTH_COOKIE  : signed HMAC cookie للمصادقة
 * FLASH_COOKIE : one-time cookie لرسائل Flash
 */

define('AUTH_COOKIE',  'sg_admin');
define('FLASH_COOKIE', 'sg_flash');

// ── Secret key ────────────────────────────────────────────────────────────────
function _authSecret(): string {
    return $_ENV['APP_SECRET'] ?? getenv('APP_SECRET') ?: 'sg-efootball-2026-secret';
}

// ── Sign / Verify ─────────────────────────────────────────────────────────────
function _cookieSign(array $data): string {
    $json = json_encode($data);
    $sig  = hash_hmac('sha256', $json, _authSecret());
    return base64_encode($json) . '.' . $sig;
}

function _cookieVerify(string $value): ?array {
    $parts = explode('.', $value, 2);
    if (count($parts) !== 2) return null;
    [$b64, $sig] = $parts;
    $json = base64_decode($b64);
    if ($json === false) return null;
    $expected = hash_hmac('sha256', $json, _authSecret());
    if (!hash_equals($expected, $sig)) return null;
    $data = json_decode($json, true);
    return is_array($data) ? $data : null;
}

// ── Flash helpers ─────────────────────────────────────────────────────────────
function setFlash(string $type, string $msg): void {
    setcookie(FLASH_COOKIE, base64_encode(json_encode(['type' => $type, 'msg' => $msg])), [
        'expires'  => time() + 60,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function getFlash(): ?array {
    $val = $_COOKIE[FLASH_COOKIE] ?? '';
    if (!$val) return null;
    setcookie(FLASH_COOKIE, '', ['expires' => 1, 'path' => '/']);
    $data = json_decode(base64_decode($val), true);
    return is_array($data) ? $data : null;
}

// ── Auth functions ────────────────────────────────────────────────────────────
function adminLogin(string $email, string $password): array
{
    // ── Local mode (no DB needed) ────────────────────────────────────────────
    if (LOCAL_AUTH) {
        $match = ($email === ADMIN_EMAIL) &&
                 (password_verify($password, ADMIN_PASSWORD) || $password === ADMIN_PASSWORD);
        if (!$match) return ['ok' => false, 'error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
        _setAuthCookie($email);
        return ['ok' => true];
    }

    // ── DB mode (PostgreSQL / Supabase) ──────────────────────────────────────
    try {
        $stmt = db()->prepare("SELECT id, password FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password'])) {
            return ['ok' => false, 'error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
        }

        _setAuthCookie($email);
        return ['ok' => true];

    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()];
    }
}

function _setAuthCookie(string $email, bool $remember = false): void {
    $days  = $remember ? 30 : 1;
    $value = _cookieSign(['email' => $email, 'ts' => time()]);
    setcookie(AUTH_COOKIE, $value, [
        'expires'  => time() + 86400 * $days,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
}

function adminLogout(): void {
    setcookie(AUTH_COOKIE,  '', ['expires' => 1, 'path' => '/']);
    setcookie(FLASH_COOKIE, '', ['expires' => 1, 'path' => '/']);
}

function isAdminLoggedIn(): bool {
    $val = $_COOKIE[AUTH_COOKIE] ?? '';
    if (!$val) return false;
    $data = _cookieVerify($val);
    return is_array($data) && !empty($data['email']);
}

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login');
        exit;
    }
}
