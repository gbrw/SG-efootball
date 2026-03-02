<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';

requireAdmin();

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── تغيير البريد الإلكتروني ───────────────────────────────────────────
    if ($action === 'email') {
        $newEmail = trim($_POST['new_email'] ?? '');
        $password = trim($_POST['confirm_password'] ?? '');

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح.';
        } elseif (empty($password)) {
            $errors[] = 'كلمة المرور الحالية مطلوبة للتأكيد.';
        } else {
            // تحقق من كلمة المرور الحالية
            $stmt = db()->prepare("SELECT password FROM admins LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($password, $admin['password'])) {
                $errors[] = 'كلمة المرور الحالية غير صحيحة.';
            } elseif ($newEmail === ($_POST['current_email'] ?? '')) {
                $errors[] = 'البريد الجديد مطابق للحالي.';
            } else {
                db()->prepare("UPDATE admins SET email = :email")->execute([':email' => $newEmail]);
                $success = 'تم تحديث البريد الإلكتروني بنجاح.';
            }
        }
    }

    // ── تغيير كلمة المرور ────────────────────────────────────────────────
    if ($action === 'password') {
        $currentPass = trim($_POST['current_password'] ?? '');
        $newPass     = trim($_POST['new_password']     ?? '');
        $confirmPass = trim($_POST['confirm_new_password'] ?? '');

        if (empty($currentPass)) {
            $errors[] = 'كلمة المرور الحالية مطلوبة.';
        } elseif (strlen($newPass) < 8) {
            $errors[] = 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل.';
        } elseif ($newPass !== $confirmPass) {
            $errors[] = 'كلمة المرور الجديدة وتأكيدها غير متطابقتين.';
        } else {
            $stmt = db()->prepare("SELECT password FROM admins LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($currentPass, $admin['password'])) {
                $errors[] = 'كلمة المرور الحالية غير صحيحة.';
            } else {
                $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
                db()->prepare("UPDATE admins SET password = :pass")->execute([':pass' => $hash]);
                $success = 'تم تحديث كلمة المرور بنجاح.';
            }
        }
    }
}

// جلب البريد الحالي
try {
    $stmt = db()->prepare("SELECT email FROM admins LIMIT 1");
    $stmt->execute();
    $currentAdmin = $stmt->fetch();
    $currentEmail = $currentAdmin['email'] ?? '';
} catch (Throwable $e) {
    $currentEmail = '';
}

$pageTitle = 'الإعدادات';
include dirname(__DIR__) . '/layouts/admin-header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="fa-solid fa-gear" style="color:var(--purple);font-size:1.3rem;"></i> الإعدادات</h1>
    <p>تغيير بيانات حساب المشرف</p>
  </div>
</div>

<?php if ($errors): ?>
<div class="flash flash-error" style="margin-bottom:1.5rem;">
  <i class="fa-solid fa-circle-exclamation"></i>
  <?= implode('<br>', array_map('h', $errors)) ?>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="flash flash-success" style="margin-bottom:1.5rem;">
  <i class="fa-solid fa-circle-check"></i> <?= h($success) ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.5rem;max-width:900px;">

  <!-- ── تغيير البريد الإلكتروني ─────────────────────────────────────── -->
  <div class="card" style="padding:1.75rem;">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;">
      <span style="display:grid;place-items:center;width:34px;height:34px;background:var(--purple-dim);border-radius:.5rem;">
        <i class="fa-solid fa-envelope" style="color:var(--purple);font-size:.9rem;"></i>
      </span>
      تغيير البريد الإلكتروني
    </h2>

    <form method="POST" action="/admin/settings">
      <input type="hidden" name="action" value="email">
      <input type="hidden" name="current_email" value="<?= h($currentEmail) ?>">

      <div class="form-group" style="margin-bottom:1rem;">
        <label class="field-label">البريد الحالي</label>
        <input type="text" class="field-input" value="<?= h($currentEmail) ?>" disabled style="opacity:.6;">
      </div>

      <div class="form-group" style="margin-bottom:1rem;">
        <label class="field-label" for="new_email">البريد الجديد</label>
        <input type="email" name="new_email" id="new_email" class="field-input"
               placeholder="example@domain.com" required>
      </div>

      <div class="form-group" style="margin-bottom:1.25rem;">
        <label class="field-label" for="confirm_password_email">كلمة المرور الحالية (للتأكيد)</label>
        <div style="position:relative;">
          <input type="password" name="confirm_password" id="confirm_password_email"
                 class="field-input" placeholder="••••••••" required style="padding-left:2.75rem;">
          <button type="button" class="toggle-pass"
                  style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-faint);cursor:pointer;"
                  onclick="togglePass(this)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;">
        <i class="fa-solid fa-floppy-disk"></i> حفظ البريد الإلكتروني
      </button>
    </form>
  </div>

  <!-- ── تغيير كلمة المرور ──────────────────────────────────────────── -->
  <div class="card" style="padding:1.75rem;">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;">
      <span style="display:grid;place-items:center;width:34px;height:34px;background:var(--cyan-dim);border-radius:.5rem;">
        <i class="fa-solid fa-lock" style="color:var(--cyan);font-size:.9rem;"></i>
      </span>
      تغيير كلمة المرور
    </h2>

    <form method="POST" action="/admin/settings">
      <input type="hidden" name="action" value="password">

      <div class="form-group" style="margin-bottom:1rem;">
        <label class="field-label" for="current_password">كلمة المرور الحالية</label>
        <div style="position:relative;">
          <input type="password" name="current_password" id="current_password"
                 class="field-input" placeholder="••••••••" required style="padding-left:2.75rem;">
          <button type="button" class="toggle-pass"
                  style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-faint);cursor:pointer;"
                  onclick="togglePass(this)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:1rem;">
        <label class="field-label" for="new_password">كلمة المرور الجديدة</label>
        <div style="position:relative;">
          <input type="password" name="new_password" id="new_password"
                 class="field-input" placeholder="8 أحرف على الأقل" required style="padding-left:2.75rem;">
          <button type="button" class="toggle-pass"
                  style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-faint);cursor:pointer;"
                  onclick="togglePass(this)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:1.25rem;">
        <label class="field-label" for="confirm_new_password">تأكيد كلمة المرور الجديدة</label>
        <div style="position:relative;">
          <input type="password" name="confirm_new_password" id="confirm_new_password"
                 class="field-input" placeholder="••••••••" required style="padding-left:2.75rem;">
          <button type="button" class="toggle-pass"
                  style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-faint);cursor:pointer;"
                  onclick="togglePass(this)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;">
        <i class="fa-solid fa-floppy-disk"></i> حفظ كلمة المرور
      </button>
    </form>
  </div>

</div>

<script>
function togglePass(btn) {
  const input = btn.closest('div').querySelector('input');
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}
</script>

<?php include dirname(__DIR__) . '/layouts/admin-footer.php'; ?>
