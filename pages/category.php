<?php
/** Category listing. Requires: $locale, $category */
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/queries.php';

$page    = max(1, (int) ($_GET['page'] ?? 1));
$posts   = getPostsByCategory($category, $locale, $page, 12);

$pageTitle = categoryLabel($category, $locale);
$pageDesc  = t('site_desc', $locale);

include dirname(__DIR__) . '/layouts/header.php';
?>

<!-- Category header -->
<div class="cat-page-header">
  <div class="container" style="padding-bottom:2rem;border-bottom:1px solid var(--border);">
    <h1 class="cat-page-title">
      <?= categoryIcon($category) ?>
      <span class="gradient-text"><?= h(categoryLabel($category, $locale)) ?></span>
    </h1>
    <div class="section-divider mt-2"></div>

    <!-- Search bar -->
    <div class="cat-search-wrap" style="margin-top:1.5rem;">
      <i class="fa-solid fa-magnifying-glass cat-search-icon"></i>
      <input type="search" id="cat-search" class="cat-search-input"
             placeholder="<?= $locale === 'ar' ? 'ابحث في المنشورات...' : 'Search posts...' ?>"
             aria-label="<?= $locale === 'ar' ? 'بحث' : 'Search' ?>">
    </div>
  </div>
</div>

<!-- Posts grid -->
<section class="py-10">
  <div class="container">
    <?php if (empty($posts)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
      <p><?= h(t('no_posts', $locale)) ?></p>
    </div>
    <?php else: ?>
    <div class="posts-grid">
      <?php foreach ($posts as $post): include dirname(__DIR__) . '/pages/partials/post-card.php'; endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if (count($posts) === 12): ?>
    <div style="display:flex;justify-content:center;gap:.75rem;margin-top:3rem;">
      <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>" class="btn btn-ghost btn-sm">← <?= $locale === 'ar' ? 'السابق' : 'Previous' ?></a>
      <?php endif; ?>
      <a href="?page=<?= $page + 1 ?>" class="btn btn-ghost btn-sm"><?= $locale === 'ar' ? 'التالي' : 'Next' ?> →</a>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>

<script>
(function () {
  var inp = document.getElementById('cat-search');
  if (!inp) return;
  var grid  = document.querySelector('.posts-grid');
  var empty = document.createElement('p');
  empty.className = 'cat-search-empty';
  empty.textContent = <?= json_encode($locale === 'ar' ? 'لا توجد نتائج مطابقة.' : 'No matching posts found.') ?>;
  if (grid) grid.parentNode.insertBefore(empty, grid.nextSibling);

  inp.addEventListener('input', function () {
    var q     = this.value.trim().toLowerCase();
    var cards = document.querySelectorAll('.posts-grid .post-card');
    var shown = 0;
    cards.forEach(function (card) {
      var title = (card.querySelector('.card-title')?.textContent || '').toLowerCase();
      var match = !q || title.includes(q);
      card.style.display = match ? '' : 'none';
      if (match) shown++;
    });
    empty.classList.toggle('visible', q.length > 0 && shown === 0);
  });
})();
</script>
