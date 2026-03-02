<?php
/**
 * Vercel PHP Router
 * يوجّه جميع الطلبات إلى الملف المناسب في جذر المشروع.
 */
ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// ── جذر المشروع (المجلد الأعلى من api/) ─────────────────────────────────
$root = dirname(__DIR__);

// ── ضمان أن require_once يعمل من الجذر ────────────────────────────────────
chdir($root);

// ── المسار المطلوب ────────────────────────────────────────────────────────
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri  = '/' . trim((string)$uri, '/');

// ── 1. ملفات ثابتة (صور، CSS، JS) — Vercel يخدمها مباشرةً ────────────────
//    لكن احتياطاً نعيد التوجيه إن وصل هنا
if (preg_match('/\.(css|js|svg|png|jpg|jpeg|webp|gif|ico|woff2?|ttf|map)$/i', $uri)) {
    http_response_code(404);
    exit;
}

// ── 2. تعيين مسارات URL إلى ملفات PHP ────────────────────────────────────
//
//  /                              → index.php
//  /ar/                           → ar/index.php
//  /en/                           → en/index.php
//  /ar/news/                      → ar/news/index.php
//  /ar/contact/                   → ar/contact/index.php
//  /ar/news/my-slug               → post.php?locale=ar&category=news&slug=my-slug
//  /admin/                        → admin/dashboard.php
//  /admin/login                   → admin/login.php
//  /admin/create etc.             → admin/{action}.php

$locales    = ['ar', 'en'];
$categories = ['news', 'formations', 'upgrades', 'leaks'];
$adminPages = ['login', 'logout', 'dashboard', 'create', 'edit', 'delete', 'upload'];

// ── جذر الموقع ──────────────────────────────────────────────────────────
if ($uri === '/') {
    chdir($root);
    require $root . '/index.php';
    exit;
}

// ── لوحة التحكم ─────────────────────────────────────────────────────────
if ($uri === '/admin' || $uri === '/admin/') {
    chdir($root . '/admin');
    require $root . '/admin/dashboard.php';
    exit;
}
if (preg_match('#^/admin/([a-z]+)(?:\.php)?/?$#', $uri, $m)) {
    $page = $m[1];
    $file = $root . '/admin/' . $page . '.php';
    if (file_exists($file)) {
        chdir($root . '/admin');
        require $file;
        exit;
    }
}

// ── /{locale}/ ──────────────────────────────────────────────────────────
if (preg_match('#^/([a-z]{2})/?$#', $uri, $m) && in_array($m[1], $locales)) {
    chdir($root . '/' . $m[1]);
    require $root . '/' . $m[1] . '/index.php';
    exit;
}

// ── /{locale}/contact/ ──────────────────────────────────────────────────
if (preg_match('#^/([a-z]{2})/contact/?$#', $uri, $m) && in_array($m[1], $locales)) {
    chdir($root . '/' . $m[1] . '/contact');
    require $root . '/' . $m[1] . '/contact/index.php';
    exit;
}

// ── /{locale}/{category}/ ────────────────────────────────────────────────
if (preg_match('#^/([a-z]{2})/([a-z]+)/?$#', $uri, $m)
    && in_array($m[1], $locales)
    && in_array($m[2], $categories)
) {
    $locale   = $m[1];
    $category = $m[2];
    chdir($root . '/' . $locale . '/' . $category);
    require $root . '/' . $locale . '/' . $category . '/index.php';
    exit;
}

// ── /{locale}/{category}/{slug} — مقال فردي ──────────────────────────────
if (preg_match('#^/([a-z]{2})/([a-z]+)/([^/]+)/?$#', $uri, $m)
    && in_array($m[1], $locales)
    && in_array($m[2], $categories)
) {
    $_GET['locale']   = $m[1];
    $_GET['category'] = $m[2];
    $_GET['slug']     = $m[3];
    chdir($root);
    require $root . '/post.php';
    exit;
}

// ── 404 ─────────────────────────────────────────────────────────────────
chdir($root);
http_response_code(404);
require $root . '/pages/404.php';
