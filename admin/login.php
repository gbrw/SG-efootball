<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (isAdminLoggedIn()) {
    header('Location: /admin/dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'الرجاء ملء جميع الحقول.';
    } else {
        $result = adminLogin($email, $password, !empty($_POST['remember']));
        if ($result['ok']) {
            header('Location: /admin/dashboard');
            exit;
        }
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول — <?= h(SITE_NAME) ?></title>
  <meta name="robots" content="noindex">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="login-wrap">
  <div class="login-box">

    <!-- Logo -->
    <div class="login-logo">
      <div class="logo" style="justify-content:center;margin-bottom:.25rem;">
        <span class="logo-icon"><i class="fa-solid fa-futbol"></i></span>
        <span class="gradient-text" style="font-size:1.4rem;font-weight:800;"><?= h(CREATOR_NAME) ?></span>
      </div>
      <p style="font-size:.82rem;color:var(--text-faint);">لوحة التحكم</p>
    </div>

    <!-- Card -->
    <div class="login-card">
      <h1>تسجيل الدخول</h1>
      <p>الوصول إلى لوحة إدارة المحتوى</p>

      <?php if (LOCAL_AUTH): ?>
      <div style="display:flex;align-items:center;gap:.5rem;background:rgba(234,179,8,.08);
                  border:1px solid rgba(234,179,8,.25);border-radius:.5rem;
                  padding:.6rem .9rem;margin-bottom:1rem;font-size:.78rem;color:#fbbf24;">
        <span><i class="fa-solid fa-screwdriver-wrench"></i></span>
        <span>وضع التطوير المحلي — بدون Supabase</span>
      </div>
      <?php endif; ?>

      <?php if ($error): ?>
      <div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group" style="margin-bottom:1.1rem;">
          <label class="field-label" for="email">البريد الإلكتروني</label>
          <input
            type="email"
            name="email"
            id="email"
            class="field-input"
            value="<?= h($_POST['email'] ?? (LOCAL_AUTH ? ADMIN_EMAIL : '')) ?>"
            placeholder="admin@example.com"
            required
            autofocus
          >
        </div>

        <div class="form-group" style="margin-bottom:1.5rem;">
          <label class="field-label" for="password">كلمة المرور</label>
          <div class="pw-wrap">
            <input
              type="password"
              name="password"
              id="password"
              class="field-input"
              placeholder="••••••••"
              required
            >
            <button type="button" class="pw-toggle" id="pw-toggle"
              aria-label="Toggle password visibility">
              <i class="fa-solid fa-eye" id="pw-eye"></i>
            </button>
          </div>
        </div>

        <!-- تذكرني -->
        <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;
                      margin-bottom:1.25rem;font-size:.88rem;color:var(--text-muted);user-select:none;">
          <input type="checkbox" name="remember" value="1"
                 style="width:16px;height:16px;accent-color:var(--purple);cursor:pointer;"
                 <?= !empty($_POST['remember']) ? 'checked' : '' ?>>
          تذكرني لمدة 30 يوماً
        </label>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
          <i class="fa-solid fa-right-to-bracket"></i> دخول
        </button>
      </form>
    </div>

  </div>
</div>

<script>
(function(){
  var btn = document.getElementById('pw-toggle');
  var inp = document.getElementById('password');
  var ico = document.getElementById('pw-eye');
  if (btn && inp && ico) {
    btn.addEventListener('click', function(){
      var isPass = inp.type === 'password';
      inp.type = isPass ? 'text' : 'password';
      ico.className = isPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
    });
  }
})();
</script>
</body>
</html>
