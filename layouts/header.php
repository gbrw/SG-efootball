<?php
/**
 * Public site header.
 * Requires: $locale, $pageTitle (optional), $pageDesc (optional)
 */
$dir       = ($locale === 'ar') ? 'rtl' : 'ltr';
$lang      = $locale;
$siteName  = t('site_name', $locale);
$siteDesc  = t('site_desc', $locale);
$title     = isset($pageTitle) ? h($pageTitle) . ' | ' . h($siteName) : h($siteName);
$desc      = isset($pageDesc)  ? h($pageDesc)  : h($siteDesc);
$canonical = SITE_URL . h($_SERVER['REQUEST_URI'] ?? '/');
$ogImage   = isset($ogImage)   ? h($ogImage)   : SITE_URL . '/assets/img/sg-banner.jpg';

$navItems = [
    '/' . $locale . '/'             => t('home',       $locale),
    '/' . $locale . '/news/'        => t('news',       $locale),
    '/' . $locale . '/formations/'  => t('formations', $locale),
    '/' . $locale . '/upgrades/'    => t('upgrades',   $locale),
    '/' . $locale . '/leaks/'       => t('leaks',      $locale),
    '/' . $locale . '/contact/'     => t('contact',    $locale),
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
  <meta name="description" content="<?= $desc ?>">
  <link rel="canonical" href="<?= $canonical ?>">

  <!-- hreflang -->
  <link rel="alternate" hreflang="ar" href="<?= SITE_URL ?>/ar/">
  <link rel="alternate" hreflang="en" href="<?= SITE_URL ?>/en/">
  <?php if (!empty($hreflangAr)): ?>
  <link rel="alternate" hreflang="ar" href="<?= h($hreflangAr) ?>">
  <link rel="alternate" hreflang="en" href="<?= h($hreflangEn ?? '') ?>">
  <?php endif; ?>

  <!-- OpenGraph -->
  <meta property="og:type"        content="<?= isset($ogType) ? h($ogType) : 'website' ?>">
  <meta property="og:title"       content="<?= $title ?>">
  <meta property="og:description" content="<?= $desc ?>">
  <meta property="og:image"       content="<?= $ogImage ?>">
  <meta property="og:url"         content="<?= $canonical ?>">
  <meta property="og:site_name"   content="<?= h($siteName) ?>">
  <meta name="twitter:card"       content="summary_large_image">
  <meta name="twitter:title"      content="<?= $title ?>">
  <meta name="twitter:image"      content="<?= $ogImage ?>">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Favicon -->
  <link rel="icon" type="image/jpeg" href="/images/1.jpg">
  <link rel="shortcut icon" href="/images/1.jpg">

  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

  <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'].'/assets/css/style.css') ?>">
  <!-- Anti-flash: set theme before first paint -->
  <script>(function(){var t=localStorage.getItem('sg-theme')||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);})();</script>
</head>
<body class="anim-fade">

<!-- ── Reading progress ───────────────────────────────────────────────── -->
<div class="reading-progress" id="reading-progress" aria-hidden="true"></div>

<!-- ── Header ─────────────────────────────────────────────────────────── -->
<header class="site-header">
  <div class="container">
    <div class="header-inner">

      <!-- Logo -->
      <a href="/<?= $locale ?>/" class="logo">
        <span class="logo-icon"><img src="/images/1.jpg" alt="<?= h(CREATOR_NAME) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;"></span>
        <span class="gradient-text"><?= h(CREATOR_NAME) ?></span>
      </a>

      <!-- Desktop nav -->
      <ul class="nav-links">
        <?php foreach ($navItems as $href => $label): ?>
        <li><a href="<?= $href ?>"><?= h($label) ?></a></li>
        <?php endforeach; ?>
      </ul>

      <!-- Right side -->
      <div class="header-right">
        <a href="<?= t('lang_switch_url', $locale) ?>" class="lang-btn">
          <i class="fa-solid fa-globe"></i><span class="lang-label"> <?= h(t('language', $locale)) ?></span>
        </a>
        <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
          <i class="fa-solid fa-moon"></i>
        </button>
        <button class="nav-toggle" id="nav-toggle" aria-label="Menu" aria-expanded="false">
          <i class="fa-solid fa-bars" style="font-size:1.1rem;"></i>
        </button>
      </div>

    </div><!-- /header-inner -->
  </div>

  <!-- Mobile menu -->
  <nav class="mobile-menu" id="mobile-menu" role="navigation">
    <?php foreach ($navItems as $href => $label): ?>
    <a href="<?= $href ?>"><?= h($label) ?></a>
    <?php endforeach; ?>
    <a href="<?= t('lang_switch_url', $locale) ?>" class="mobile-lang">
      <i class="fa-solid fa-globe"></i> <?= h(t('language', $locale)) ?>
    </a>
  </nav>
</header>

<main>
