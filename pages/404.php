<?php
/** 404 page. Can be called standalone or included. */
if (!defined('SITE_URL')) {
    require_once dirname(__DIR__) . '/includes/config.php';
    require_once dirname(__DIR__) . '/includes/functions.php';
}

$locale    = $locale ?? 'ar';
$pageTitle = t('not_found', $locale);

include dirname(__DIR__) . '/layouts/header.php';
?>

<div class="container page-404">
  <div class="err-code gradient-text">404</div>
  <h1 style="font-size:1.5rem;color:var(--white);"><?= h(t('not_found', $locale)) ?></h1>
  <p><?= h(t('not_found_desc', $locale)) ?></p>
  <a href="/<?= $locale ?>/" class="btn btn-primary"><?= h(t('back_home', $locale)) ?></a>
</div>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
