<?php /** Requires: $locale */ ?>
</main>

<!-- ── Footer ─────────────────────────────────────────────────────────── -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-inner">

      <div class="footer-brand">
        <div class="logo">
          <span class="logo-icon"><img src="/images/1.jpg" alt="<?= h(CREATOR_NAME) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;"></span>
          <span class="gradient-text"><?= h(CREATOR_NAME) ?></span>
        </div>
        <p><?= h(t('site_desc', $locale)) ?></p>
        <div class="footer-social">
          <a href="https://www.youtube.com/@SG-Efootball1"   class="social-btn social-btn-yt" target="_blank" rel="noopener" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
          <a href="https://x.com/SG_Efootball1"             class="social-btn social-btn-x"  target="_blank" rel="noopener" title="X / Twitter"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="https://www.instagram.com/sg_efootball1/" class="social-btn social-btn-ig" target="_blank" rel="noopener" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="https://www.tiktok.com/@sg_efootball1"   class="social-btn social-btn-tt" target="_blank" rel="noopener" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
          <a href="https://discord.gg/qXz85w3Hyc"             class="social-btn social-btn-dc" target="_blank" rel="noopener" title="Discord"><i class="fa-brands fa-discord"></i></a>
          <a href="https://t.me/SG_efootball1"              class="social-btn social-btn-tg" target="_blank" rel="noopener" title="Telegram"><i class="fa-brands fa-telegram"></i></a>
        </div>
      </div>

      <div class="footer-nav-col">
        <h4><?= $locale === 'ar' ? 'الصفحات' : 'Pages' ?></h4>
        <ul>
          <?php foreach ([
            '/' . $locale . '/'            => t('home',       $locale),
            '/' . $locale . '/news/'       => t('news',       $locale),
            '/' . $locale . '/formations/' => t('formations', $locale),
            '/' . $locale . '/upgrades/'   => t('upgrades',   $locale),
            '/' . $locale . '/leaks/'      => t('leaks',      $locale),
            '/' . $locale . '/contact/'    => t('contact',    $locale),
          ] as $href => $label): ?>
          <li><a href="<?= $href ?>"><?= h($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> <?= h(CREATOR_NAME) ?> &mdash; <?= h(t('rights', $locale)) ?></span>
      <span><?= t('made_with', $locale) ?></span>
    </div>

  </div>
</footer>

<script src="/assets/js/main.js"></script>

<!-- Back to top -->
<button class="back-to-top" id="back-to-top" aria-label="Back to top">
  <i class="fa-solid fa-chevron-up"></i>
</button>
</body>
</html>
