<?php
/** Shared post-card snippet. Requires: $post, $locale (included context) */
$tr  = getTranslation($post);
if (!$tr) return;
$cat  = $post['category'];
$href = '/' . $locale . '/' . $cat . '/' . $tr['slug'] . '/';
?>
<a href="<?= h($href) ?>" class="card post-card reveal">
  <div class="thumb">
    <img data-src="<?= h($post['image_url']) ?>" src="/assets/img/placeholder.svg" alt="<?= h($tr['title']) ?>" loading="lazy">
    <div class="badge-wrap">
      <span class="badge <?= categoryBadgeClass($cat) ?>">
        <?= categoryIcon($cat) ?> <?= h(categoryLabel($cat, $locale)) ?>
      </span>
    </div>
  </div>
  <div class="card-body">
    <h3 class="card-title"><?= h($tr['title']) ?></h3>
    <div class="card-meta">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <?= formatDate($post['created_at'], $locale) ?>
      <span class="arrow-icon">→</span>
    </div>
  </div>
</a>
