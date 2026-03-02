<?php
/** Contact page template. Requires: $locale */
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$pageTitle = t('contact_title', $locale);

include dirname(__DIR__) . '/layouts/header.php';

$socials = [
    ['cls'=>'yt', 'icon'=>'<i class="fa-brands fa-youtube"   style="font-size:1.6rem;color:#FF0000;"></i>', 'platform'=>t('youtube',   $locale), 'handle'=>'@SG-Efootball1', 'url'=>'https://www.youtube.com/@SG-Efootball1'],
    ['cls'=>'x',  'icon'=>'<i class="fa-brands fa-x-twitter" style="font-size:1.6rem;color:#E7E7E7;"></i>', 'platform'=>t('twitter',   $locale), 'handle'=>'@SG_Efootball1', 'url'=>'https://x.com/SG_Efootball1'],
    ['cls'=>'ig', 'icon'=>'<i class="fa-brands fa-instagram" style="font-size:1.6rem;color:#E1306C;"></i>', 'platform'=>t('instagram', $locale), 'handle'=>'@sg_efootball1', 'url'=>'https://www.instagram.com/sg_efootball1/'],
    ['cls'=>'tt', 'icon'=>'<i class="fa-brands fa-tiktok"    style="font-size:1.6rem;color:#69C9D0;"></i>', 'platform'=>t('tiktok',    $locale), 'handle'=>'@sg_efootball1', 'url'=>'https://www.tiktok.com/@sg_efootball1'],
    ['cls'=>'fb', 'icon'=>'<i class="fa-brands fa-facebook"  style="font-size:1.6rem;color:#1877F2;"></i>', 'platform'=>t('facebook',  $locale), 'handle'=>'SG Efootball1',  'url'=>'https://www.facebook.com/people/SG-Efootball1/100090210769391/'],
    ['cls'=>'th', 'icon'=>'<i class="fa-brands fa-threads"   style="font-size:1.6rem;color:#A8A8A8;"></i>', 'platform'=>t('threads',   $locale), 'handle'=>'@sg_efootball1', 'url'=>'https://www.threads.com/@sg_efootball1'],
    ['cls'=>'dc', 'icon'=>'<i class="fa-brands fa-discord"   style="font-size:1.6rem;color:#5865F2;"></i>', 'platform'=>t('discord',   $locale), 'handle'=>'discord.gg/qXz85w3Hyc', 'url'=>'https://discord.gg/qXz85w3Hyc'],
    ['cls'=>'tg', 'icon'=>'<i class="fa-brands fa-telegram"  style="font-size:1.6rem;color:#26A5E4;"></i>', 'platform'=>t('telegram',  $locale), 'handle'=>'@SG_efootball1', 'url'=>'https://t.me/SG_efootball1'],
];
?>

<div class="container-sm py-16">
  <div class="text-center mb-8">
    <h1 class="section-title gradient-text"><?= h(t('contact_title', $locale)) ?></h1>
    <div class="section-divider" style="margin:0 auto .75rem;"></div>
    <p style="color:var(--text-muted);font-size:1.05rem;"><?= h(t('contact_sub', $locale)) ?></p>
  </div>

  <div class="contact-grid">
    <?php foreach ($socials as $s): ?>
    <a href="<?= h($s['url']) ?>" target="_blank" rel="noopener" class="contact-card <?= $s['cls'] ?>">
      <div class="contact-icon"><?= $s['icon'] ?></div>
      <div>
        <div class="contact-platform"><?= h($s['platform']) ?></div>
        <div class="contact-handle"><?= h($s['handle']) ?></div>
      </div>
      <span style="font-size:.8rem;color:var(--text-faint);"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
