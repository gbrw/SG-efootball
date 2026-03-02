<?php
/** Root — redirect to default locale */
require_once __DIR__ . '/includes/config.php';

// Locale detection via browser Accept-Language
$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'ar';
$locale = str_starts_with($lang, 'en') ? 'en' : 'ar';

header('Location: /' . $locale . '/');
exit;
