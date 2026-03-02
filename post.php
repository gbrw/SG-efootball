<?php
/**
 * Single post page.
 * URL pattern (via .htaccess): /{locale}/{category}/{slug}/
 * Query params: locale, category, slug
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/queries.php';

$locale   = $_GET['locale']   ?? 'ar';
$category = $_GET['category'] ?? '';
$slug     = $_GET['slug']     ?? '';

if (!in_array($locale, LOCALES) || !in_array($category, CATEGORIES) || empty($slug)) {
    http_response_code(404);
    include __DIR__ . '/pages/404.php';
    exit;
}

$post = getPostBySlug($slug, $locale);

if (!$post || $post['category'] !== $category) {
    http_response_code(404);
    include __DIR__ . '/pages/404.php';
    exit;
}

$tr = getTranslation($post);
if (!$tr) {
    http_response_code(404);
    include __DIR__ . '/pages/404.php';
    exit;
}

// hreflang alternates
$alts = getPostAlternates($post['id']);
$otherLoc = otherLocale($locale);
$hreflangAr = isset($alts['ar']) ? SITE_URL . '/ar/' . $category . '/' . $alts['ar'] . '/' : '';
$hreflangEn = isset($alts['en']) ? SITE_URL . '/en/' . $category . '/' . $alts['en'] . '/' : '';

$pageTitle = $tr['title'];
$pageDesc  = mb_substr(strip_tags($tr['content']), 0, 160);
$ogImage   = $post['image_url'];
$ogType    = 'article';

$ytId = $post['video_url'] ? getYouTubeId($post['video_url']) : null;

include __DIR__ . '/layouts/header.php';
?>

<div class="container-sm">

  <!-- Breadcrumb -->
  <nav class="breadcrumb" aria-label="breadcrumb">
    <a href="/<?= $locale ?>/"><?= h(t('home', $locale)) ?></a>
    <span>›</span>
    <a href="/<?= $locale ?>/<?= $category ?>/"><?= h(categoryLabel($category, $locale)) ?></a>
    <span>›</span>
    <span style="color:var(--text-muted);"><?= h(mb_substr($tr['title'], 0, 40)) ?>…</span>
  </nav>

  <!-- Meta -->
  <div class="post-meta">
    <span class="badge <?= categoryBadgeClass($category) ?>">
      <?= categoryIcon($category) ?> <?= h(categoryLabel($category, $locale)) ?>
    </span>
    <span class="post-date">
      <i class="fa-regular fa-calendar" style="font-size:.85em;"></i> <?= h(t('published', $locale)) ?> <?= formatDate($post['created_at'], $locale) ?>
    </span>
    <?php if ($post['updated_at'] !== $post['created_at']): ?>
    <span class="post-date">
      <i class="fa-solid fa-rotate" style="font-size:.85em;"></i> <?= h(t('updated', $locale)) ?> <?= formatDate($post['updated_at'], $locale) ?>
    </span>
    <?php endif; ?>
  </div>

  <!-- Title -->
  <h1 class="post-title"><?= h($tr['title']) ?></h1>

  <!-- Hero image -->
  <img
    src="<?= h($post['image_url']) ?>"
    alt="<?= h($tr['title']) ?>"
    class="post-hero-img"
  >

  <!-- YouTube embed -->
  <?php if ($ytId): ?>
  <div class="video-embed">
    <iframe
      src="https://www.youtube-nocookie.com/embed/<?= h($ytId) ?>"
      title="<?= h($tr['title']) ?>"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
      allowfullscreen
    ></iframe>
  </div>
  <?php endif; ?>

  <!-- Content -->
  <div class="post-content">
    <?= $tr['content'] /* Already HTML from DB — sanitize on input */ ?>
  </div>

  <!-- Back link -->
  <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--border);">
    <a href="/<?= $locale ?>/<?= $category ?>/" class="btn btn-ghost btn-sm">
      ← <?= h(t('back_to', $locale)) ?> <?= h(categoryLabel($category, $locale)) ?>
    </a>
  </div>

</div>

<div style="margin-bottom:4rem;"></div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
