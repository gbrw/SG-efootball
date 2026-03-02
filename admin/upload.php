<?php
/**
 * AJAX image upload endpoint.
 * على Vercel: يرفع الصور إلى Supabase Storage ويُرجع الرابط العام.
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
$filename    = date('Y/m') . '/' . bin2hex(random_bytes(10)) . '.' . $ext;
$supabaseUrl = rtrim(SUPABASE_URL, '/');
$serviceKey  = SUPABASE_SERVICE_ROLE_KEY;
$bucket      = SUPABASE_BUCKET;

if (!$supabaseUrl || !$serviceKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Supabase غير مضبوط. تحقق من SUPABASE_URL و SUPABASE_SERVICE_ROLE_KEY']);
    exit;
}

// ── Upload to Supabase Storage ────────────────────────────────────────────
$uploadEndpoint = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$filename}";
$fileContent    = file_get_contents($file['tmp_name']);

$ch = curl_init($uploadEndpoint);
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_POSTFIELDS     => $fileContent,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $serviceKey,
        'Content-Type: ' . $mimeType,
        'x-upsert: true',
    ],
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) {
    $err = json_decode($response, true)['message'] ?? $response;
    http_response_code(500);
    echo json_encode(['error' => 'فشل الرفع إلى Supabase Storage: ' . $err]);
    exit;
}

$publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
echo json_encode(['url' => $publicUrl]);
exit;
