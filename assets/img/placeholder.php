<?php
/**
 * Image placeholder generator
 * Usage: /assets/img/placeholder.php?w=400&h=400&text=SG
 */
$w    = min((int)($_GET['w'] ?? 400), 1920);
$h    = min((int)($_GET['h'] ?? 400), 1080);
$text = preg_replace('/[^A-Za-z0-9 _\-]/', '', $_GET['text'] ?? 'SG');

header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=86400');
echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}">
  <defs>
    <radialGradient id="bg" cx="50%" cy="50%" r="70%">
      <stop offset="0%"   stop-color="#1e1a30"/>
      <stop offset="100%" stop-color="#09090F"/>
    </radialGradient>
    <linearGradient id="txt" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%"   stop-color="#A855F7"/>
      <stop offset="100%" stop-color="#22D3EE"/>
    </linearGradient>
  </defs>
  <rect width="{$w}" height="{$h}" fill="url(#bg)"/>
  <text x="50%" y="54%" text-anchor="middle" dominant-baseline="middle"
        font-family="Inter,sans-serif" font-weight="900"
        font-size="<?= (int)($h * .28) ?>" fill="url(#txt)" opacity=".9">{$text}</text>
  <text x="50%" y="76%" text-anchor="middle" dominant-baseline="middle"
        font-family="Inter,sans-serif" font-weight="600"
        font-size="<?= (int)($h * .06) ?>" fill="#94A3B8" letter-spacing="4">EFOOTBALL</text>
</svg>
SVG;
