<?php

// ─── Locale helpers ───────────────────────────────────────────────────────────

function currentLocale(): string
{
    $seg = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $first = explode('/', $seg)[0] ?? 'ar';
    return in_array($first, LOCALES) ? $first : 'ar';
}

function isRtl(string $locale): bool
{
    return $locale === 'ar';
}

function localeName(string $locale): string
{
    return $locale === 'ar' ? 'العربية' : 'English';
}

function otherLocale(string $locale): string
{
    return $locale === 'ar' ? 'en' : 'ar';
}

// ─── Translations ─────────────────────────────────────────────────────────────

function t(string $key, string $locale): string
{
    static $translations = [];
    if (empty($translations[$locale])) {
        $file = dirname(__DIR__) . "/lang/{$locale}.php";
        $translations[$locale] = file_exists($file) ? include $file : [];
    }
    return $translations[$locale][$key] ?? $key;
}

// ─── Date formatting ──────────────────────────────────────────────────────────

function formatDate(string $dateStr, string $locale): string
{
    $ts = strtotime($dateStr);
    if ($locale === 'ar') {
        $months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو',
                   'يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
        $d = (int) date('d', $ts);
        $m = $months[(int) date('n', $ts) - 1];
        $y = date('Y', $ts);
        return "{$d} {$m} {$y}";
    }
    return date('F j, Y', $ts);
}

// ─── Slugify ──────────────────────────────────────────────────────────────────

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[\s_]+/', '-', $text);
    $text = preg_replace('/[^\p{L}\p{N}\-]/u', '', $text);
    $text = preg_replace('/--+/', '-', $text);
    return trim($text, '-');
}

// ─── Category helpers ─────────────────────────────────────────────────────────

function categoryLabel(string $cat, string $locale): string
{
    $labels = [
        'ar' => ['news'=>'الأخبار','formations'=>'التشكيلات','upgrades'=>'الترقيات','leaks'=>'التسريبات'],
        'en' => ['news'=>'News','formations'=>'Formations','upgrades'=>'Upgrades','leaks'=>'Leaks'],
    ];
    return $labels[$locale][$cat] ?? ucfirst($cat);
}

function categoryIcon(string $cat): string
{
    $icons = [
        'news'       => '<i class="fa-solid fa-newspaper"></i>',
        'formations' => '<i class="fa-solid fa-chess"></i>',
        'upgrades'   => '<i class="fa-solid fa-arrow-trend-up"></i>',
        'leaks'      => '<i class="fa-solid fa-eye-slash"></i>',
    ];
    return $icons[$cat] ?? '<i class="fa-solid fa-file"></i>';
}

function categoryBadgeClass(string $cat): string
{
    return match($cat) {
        'news'       => 'badge-blue',
        'formations' => 'badge-green',
        'upgrades'   => 'badge-purple',
        'leaks'      => 'badge-orange',
        default      => 'badge-blue',
    };
}

// ─── YouTube helpers ─────────────────────────────────────────────────────────

function getYouTubeId(string $url): ?string
{
    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w\-]{11})/', $url, $m)) {
        return $m[1];
    }
    return null;
}

function getYouTubeThumbnail(string $url): ?string
{
    $id = getYouTubeId($url);
    return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
}

// ─── HTML helpers ─────────────────────────────────────────────────────────────

function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    return SITE_URL . '/' . ltrim($path, '/');
}

// ─── Pagination ───────────────────────────────────────────────────────────────

function paginate(int $total, int $perPage, int $current): array
{
    $pages = (int) ceil($total / $perPage);
    return ['total' => $total, 'pages' => $pages, 'current' => $current, 'perPage' => $perPage];
}
