<?php
require_once __DIR__ . '/config.php';

/**
 * Admin login.
 * LOCAL_AUTH=true  → compare .env ADMIN_EMAIL / ADMIN_PASSWORD (plain or hash)
 * LOCAL_AUTH=false → compare against admins table in MySQL
 */
function adminLogin(string $email, string $password): array
{
    // ── Local mode (no DB needed) ─────────────────────────────────────────────
    if (LOCAL_AUTH) {
        $match = ($email === ADMIN_EMAIL) &&
                 (password_verify($password, ADMIN_PASSWORD) || $password === ADMIN_PASSWORD);
        if (!$match) return ['ok' => false, 'error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];

        $_SESSION['logged_in']    = true;
        $_SESSION['access_token'] = 'local';
        $_SESSION['user_email']   = $email;
        return ['ok' => true];
    }

    // ── MySQL mode ────────────────────────────────────────────────────────────
    try {
        $stmt = db()->prepare("SELECT id, password FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password'])) {
            return ['ok' => false, 'error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
        }

        $_SESSION['logged_in']    = true;
        $_SESSION['access_token'] = 'db_' . $admin['id'];
        $_SESSION['user_email']   = $email;
        return ['ok' => true];

    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()];
    }
}

function adminLogout(): void
{
    session_destroy();
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['access_token']);
}

function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}
