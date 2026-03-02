<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';

requireAdmin();

// Fetch all posts with both translations
$posts   = [];
$dbError = '';
try {
    $stmt = db()->query(
        "SELECT p.id, p.category, p.image_url, p.created_at,
                MAX(CASE WHEN pt.language='ar' THEN pt.title END) AS ar_title,
                MAX(CASE WHEN pt.language='en' THEN pt.title END) AS en_title,
                MAX(CASE WHEN pt.language='ar' THEN pt.slug  END) AS ar_slug,
                MAX(CASE WHEN pt.language='en' THEN pt.slug  END) AS en_slug
         FROM posts p
         LEFT JOIN post_translations pt ON pt.post_id = p.id
         GROUP BY p.id
         ORDER BY p.created_at DESC"
    );
    $posts = $stmt->fetchAll();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

// Flash messages
$flash = getFlash();

$pageTitle = 'Dashboard';
include dirname(__DIR__) . '/layouts/admin-header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="fa-solid fa-gauge" style="color:var(--purple);font-size:1.3rem;"></i> لوحة التحكم</h1>
    <p><?= count($posts) ?> منشور إجمالي</p>
  </div>
  <a href="/admin/create" class="btn btn-primary"><i class="fa-solid fa-pen"></i> منشور جديد</a>
</div>

<?php
// Count per category
$catCounts = array_count_values(array_column($posts, 'category'));
$statsMap = [
  'news'       => ['icon' => 'fa-solid fa-newspaper',       'color' => 'purple', 'label' => 'الأخبار'],
  'formations' => ['icon' => 'fa-solid fa-chess-board',     'color' => 'cyan',   'label' => 'التشكيلات'],
  'upgrades'   => ['icon' => 'fa-solid fa-arrow-trend-up',  'color' => 'green',  'label' => 'تطويرات اللاعبين'],
  'leaks'      => ['icon' => 'fa-solid fa-circle-radiation', 'color' => 'orange', 'label' => 'التسريبات'],
];
?>
<div class="admin-stats">
  <div class="admin-stat-card">
    <div class="admin-stat-icon purple"><i class="fa-solid fa-layer-group"></i></div>
    <div>
      <div class="admin-stat-val"><?= count($posts) ?></div>
      <div class="admin-stat-lbl">إجمالي المنشورات</div>
    </div>
  </div>
  <?php foreach ($statsMap as $cat => $s): ?>
  <div class="admin-stat-card">
    <div class="admin-stat-icon <?= $s['color'] ?>"><i class="<?= $s['icon'] ?>"></i></div>
    <div>
      <div class="admin-stat-val"><?= $catCounts[$cat] ?? 0 ?></div>
      <div class="admin-stat-lbl"><?= $s['label'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if (LOCAL_AUTH): ?>
<div style="display:flex;align-items:center;gap:.6rem;background:rgba(234,179,8,.07);
            border:1px solid rgba(234,179,8,.2);border-radius:.6rem;
            padding:.7rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:#fbbf24;">
  <span style="font-size:1rem;"><i class="fa-solid fa-screwdriver-wrench"></i></span>
  <span><strong>وضع التطوير المحلي</strong> — اللوجن محلي بدون قاعدة بيانات.
        الصور تُحفظ في <code style="background:rgba(0,0,0,.3);padding:.1em .4em;border-radius:.3em;">/assets/uploads/</code>.
        لاستخدام MySQL: غيّر <code style="background:rgba(0,0,0,.3);padding:.1em .4em;border-radius:.3em;">LOCAL_AUTH=false</code> في <code style="background:rgba(0,0,0,.3);padding:.1em .4em;border-radius:.3em;">.env</code>.</span>
</div>
<?php endif; ?>

<?php if ($flash): ?>
<div class="flash flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
  <?= h($flash['msg']) ?>
</div>
<?php endif; ?>

<?php if ($dbError): ?>
<div class="flash flash-error">
  <i class="fa-solid fa-circle-exclamation"></i> <?= h($dbError) ?>
</div>
<?php endif; ?>


<div class="card">
  <div style="overflow-x:auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th></th>
          <th>العنوان (عربي)</th>
          <th>العنوان (إنجليزي)</th>
          <th>التصنيف</th>
          <th>التاريخ</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($posts)): ?>
        <tr>
          <td colspan="6" style="text-align:center;color:var(--text-faint);padding:3rem;">
            لا توجد منشورات بعد. <a href="/admin/create" style="color:var(--purple);">أضف أول منشور ←</a>
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($posts as $p): ?>
        <tr>
          <td style="width:60px;">
            <?php if (!empty($p['image_url'])): ?>
            <img src="<?= h($p['image_url']) ?>" class="thumb-sm" alt="" loading="lazy">
            <?php else: ?>
            <div style="width:52px;height:36px;border-radius:var(--radius-xs);background:var(--bg-hover);border:1px solid var(--border);"></div>
            <?php endif; ?>
          </td>
          <td class="col-title truncate" style="max-width:180px;"><?= h($p['ar_title'] ?? '—') ?></td>
          <td class="col-title truncate" style="max-width:180px;color:var(--text-muted);font-weight:500;"><?= h($p['en_title'] ?? '—') ?></td>
          <td>
            <span class="badge <?= categoryBadgeClass($p['category']) ?>">
              <?= categoryIcon($p['category']) ?> <?= h(categoryLabel($p['category'], 'ar')) ?>
            </span>
          </td>
          <td class="col-date"><?= formatDate($p['created_at'], 'en') ?></td>
          <td>
            <div class="actions">
              <?php if (!empty($p['en_slug'])): ?>
              <a href="/en/<?= h($p['category']) ?>/<?= h($p['en_slug']) ?>/"
                 target="_blank" class="btn btn-ghost btn-sm" title="عرض"><i class="fa-solid fa-eye"></i></a>
              <?php endif; ?>
              <a href="/admin/edit?id=<?= h($p['id']) ?>"
                 class="btn btn-ghost btn-sm" title="تعديل"><i class="fa-solid fa-pen-to-square"></i></a>
              <form method="POST" action="/admin/delete" style="display:inline;">
                <input type="hidden" name="id" value="<?= h($p['id']) ?>">
                <button type="submit" class="btn btn-danger btn-sm"
                  data-confirm="حذف هذا المنشور؟ لا يمكن التراجع عن هذا الإجراء."><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include dirname(__DIR__) . '/layouts/admin-footer.php'; ?>
