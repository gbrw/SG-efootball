<?php
/** Admin panel header — requires $pageTitle */
$adminTitle = isset($pageTitle) ? h($pageTitle) . ' — لوحة التحكم' : 'لوحة التحكم';
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

$adminNav = [
    '/admin/dashboard.php' => ['icon' => 'fa-solid fa-gauge',        'label' => 'الرئيسية'],
    '/admin/create.php'    => ['icon' => 'fa-solid fa-pen-to-square', 'label' => 'منشور جديد'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $adminTitle ?></title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

  <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'].'/assets/css/style.css') ?>">

  <style>
    /* Admin always uses Cairo for Arabic UX */
    body { font-family: 'Cairo', system-ui, sans-serif; direction: rtl; }

    /* Stats grid */
    .admin-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }
    .admin-stat-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.25rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      transition: border-color var(--t-base) var(--ease), transform var(--t-base) var(--ease);
    }
    .admin-stat-card:hover { border-color: rgba(192,132,252,.4); transform: translateY(-2px); }
    .admin-stat-icon {
      width: 44px; height: 44px;
      border-radius: var(--radius-sm);
      display: grid; place-items: center;
      font-size: 1.15rem;
      flex-shrink: 0;
    }
    .admin-stat-icon.purple { background: var(--purple-dim); color: var(--purple); }
    .admin-stat-icon.cyan   { background: var(--cyan-dim);   color: var(--cyan); }
    .admin-stat-icon.green  { background: rgba(52,211,153,.1); color: #34d399; }
    .admin-stat-icon.orange { background: rgba(251,146,60,.1); color: #fb923c; }
    .admin-stat-val  { font-size: 1.75rem; font-weight: 800; color: var(--white); line-height: 1; }
    .admin-stat-lbl  { font-size: .78rem; color: var(--text-faint); margin-top: .2rem; }

    /* Editor toolbar */
    .editor-toolbar {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: .3rem;
      padding: .5rem .65rem;
      background: var(--bg-hover);
      border: 1px solid var(--border);
      border-bottom: none;
      border-radius: var(--radius-sm) var(--radius-sm) 0 0;
    }
    .editor-toolbar + .field-input { border-radius: 0 0 var(--radius-sm) var(--radius-sm); }
    .tb-btn {
      display: inline-flex; align-items: center; gap: .3rem;
      padding: .28rem .55rem;
      border-radius: .35rem;
      border: 1px solid var(--border);
      background: var(--bg-card);
      color: var(--text-muted);
      font-size: .8rem;
      cursor: pointer;
      font-family: inherit;
      transition: all .15s;
      white-space: nowrap;
    }
    .tb-btn:hover { border-color: var(--purple); color: var(--purple); background: var(--purple-dim); }
    .tb-sep { width: 1px; height: 20px; background: var(--border); margin: 0 .15rem; }
    .tb-img-btn { color: var(--cyan); border-color: rgba(34,211,238,.3); background: rgba(34,211,238,.06); font-weight: 600; }
    .tb-img-btn:hover { border-color: var(--cyan); background: rgba(34,211,238,.12); color: var(--cyan); }
  </style>

  <!-- Prevent dark/light flash -->
  <script>
    (function(){
      var t = localStorage.getItem('sg-theme') ||
              (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', t);
    })();
  </script>
</head>
<body<?php if (isset($bodyMode)) echo ' data-mode="' . h($bodyMode) . '"'; ?>>

<div class="admin-wrap">

  <!-- Sidebar overlay (mobile) -->
  <div class="admin-overlay" id="admin-overlay"></div>

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar-brand">
      <span class="logo-icon"><img src="/images/1.jpg" alt="admin" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;"></span>
      <span class="gradient-text" style="font-weight:800;font-size:.95rem;">لوحة التحكم</span>
    </div>

    <nav class="admin-nav">
      <?php foreach ($adminNav as $href => $item): ?>
      <a href="<?= $href ?>" class="<?= str_starts_with($currentUri, $href) ? 'active' : '' ?>">
        <i class="<?= $item['icon'] ?>"></i> <?= h($item['label']) ?>
      </a>
      <?php endforeach; ?>
      <a href="/ar/" target="_blank" style="margin-top:.5rem;">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> عرض الموقع
      </a>
    </nav>

    <div class="admin-sidebar-footer">
      <!-- Dark / Light toggle -->
      <button class="btn btn-ghost btn-sm" id="theme-toggle"
              style="width:100%;justify-content:flex-start;gap:.6rem;"
              aria-label="تغيير المظهر">
        <i class="fa-solid fa-moon"></i>
        <span data-theme-label>الوضع الليلي</span>
      </button>
      <!-- Logout -->
      <form action="/admin/logout.php" method="POST">
        <button type="submit" class="btn btn-danger btn-sm"
                style="width:100%;justify-content:flex-start;gap:.6rem;">
          <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
        </button>
      </form>
    </div>
  </aside>

  <!-- Main -->
  <main class="admin-main">

    <!-- Mobile top bar -->
    <div class="admin-mobile-bar">
      <button class="admin-menu-btn" id="admin-menu-btn" aria-label="القائمة">
        <i class="fa-solid fa-bars"></i>
      </button>
      <span class="gradient-text" style="font-weight:800;font-size:.95rem;">لوحة التحكم</span>
    </div>
