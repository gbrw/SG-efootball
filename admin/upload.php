<?php
/**
 * AJAX image upload endpoint.
 * Saves files to /assets/uploads/YYYY/MM/ and returns the public URL.
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isAdminLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'غير مصرح']);
    exit;
}

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $code = $_FILES['image']['error'] ?? -1;
    echo json_encode(['error' => "خطأ في الرفع: كود {$code}"]);
    exit;
}

$file = $_FILES['image'];

// Validate MIME
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
$allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
if (!in_array($mimeType, $allowed)) {
    echo json_encode(['error' => 'نوع الملف غير مسموح. المسموح: JPEG، PNG، WebP، GIF']);
    exit;
}

// Validate size (5 MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['error' => 'حجم الملف كبير. الحد الأقصى 5 ميغابايت']);
    exit;
}

$ext = match($mimeType) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    default      => 'jpg',
};
$filename = bin2hex(random_bytes(10)) . '.' . $ext;

// ── Save to /assets/uploads/ ──────────────────────────────────────────────
$uploadDir = dirname(__DIR__) . '/assets/uploads/' . date('Y/m') . '/';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
    http_response_code(500);
    echo json_encode(['error' => 'تعذّر إنشاء مجلد الرفع']);
    exit;
}

$destPath = $uploadDir . $filename;
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'فشل حفظ الملف']);
    exit;
}

$publicUrl = rtrim(SITE_URL, '/') . '/assets/uploads/' . date('Y/m') . '/' . $filename;
echo json_encode(['url' => $publicUrl]);
exit;
