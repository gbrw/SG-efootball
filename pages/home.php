<?php
/** Homepage template. Requires: $locale */
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/queries.php';

$pageTitle = t('site_name', $locale);
$pageDesc  = t('site_desc', $locale);

$latestPosts = getLatestPosts($locale, 6);

include dirname(__DIR__) . '/layouts/header.php';
?>

<!-- ── HERO ──────────────────────────────────────────────────────────── -->
<?php
  // Banner image used as hero background - fallback to gradient if missing
  $bannerImg = file_exists(dirname(__DIR__).'/assets/img/sg-banner.jpg')
    ? '/assets/img/sg-banner.jpg'
    : '/assets/img/placeholder.php?w=1500&h=422&text=SG';
  $avatarImg = file_exists(dirname(__DIR__).'/assets/img/sg-avatar.jpg')
    ? '/assets/img/sg-avatar.jpg'
    : '/assets/img/placeholder.php?w=400&h=400&text=SG';
?>
<section class="hero hero-banner">
  <div class="hero-bg-layer" style="background-image:url('<?= $bannerImg ?>')"></div>
  <div class="hero-overlay"></div>
  <div class="container">
    <div class="hero-inner">

      <!-- Text -->
      <div class="anim-slide">
        <div class="hero-tag">
          <span class="dot"></span>
          eFootball Content Creator &amp; GS Team Coach
        </div>

        <h1 class="hero-title">
          <?= h(t('greeting', $locale)) ?>
          <br>
          <span class="gradient-text"><?= h(t('creator_name', $locale)) ?></span>
        </h1>

        <p class="hero-bio"><?= h(t('hero_bio', $locale)) ?></p>

        <div class="hero-socials">
          <a href="https://www.youtube.com/@SG-Efootball1" target="_blank" rel="noopener" class="hero-social-btn"><i class="fa-brands fa-youtube"></i> YouTube</a>
          <a href="https://x.com/SG_Efootball1" target="_blank" rel="noopener" class="hero-social-btn"><i class="fa-brands fa-x-twitter"></i> Twitter</a>
          <a href="https://www.instagram.com/sg_efootball1/" target="_blank" rel="noopener" class="hero-social-btn"><i class="fa-brands fa-instagram"></i> Instagram</a>
          <a href="https://www.tiktok.com/@sg_efootball1" target="_blank" rel="noopener" class="hero-social-btn"><i class="fa-brands fa-tiktok"></i> TikTok</a>
          <a href="https://discord.gg/qXz85w3Hyc" target="_blank" rel="noopener" class="hero-social-btn"><i class="fa-brands fa-discord"></i> Discord</a>
          <a href="https://t.me/SG_efootball1" target="_blank" rel="noopener" class="hero-social-btn"><i class="fa-brands fa-telegram"></i> Telegram</a>
        </div>

        <a href="/<?= $locale ?>/news/" class="btn btn-primary">
          <?= h(t('explore', $locale)) ?> <i class="fa-solid fa-arrow-right" style="font-size:.8rem;"></i>
        </a>
      </div>

      <!-- Avatar -->
      <div class="hero-avatar">
        <div class="avatar-wrap">
          <div class="avatar-glow"></div>
          <img
            class="avatar-img"
            src="<?= $avatarImg ?>"
            alt="<?= h(t('creator_name', $locale)) ?>"
          >
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── LATEST POSTS ───────────────────────────────────────────────────── -->
<section class="py-16">
  <div class="container">
    <div class="section-head reveal">
      <div>
        <h2 class="section-title gradient-text"><?= h(t('latest_posts', $locale)) ?></h2>
        <div class="section-divider"></div>
      </div>
      <a href="/<?= $locale ?>/news/" class="view-all"><?= h(t('view_all', $locale)) ?> →</a>
    </div>

    <?php if (empty($latestPosts)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
      <p><?= h(t('no_posts', $locale)) ?></p>
    </div>
    <?php else: ?>
    <div class="posts-grid">
      <?php foreach ($latestPosts as $post): include dirname(__DIR__) . '/pages/partials/post-card.php'; endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
