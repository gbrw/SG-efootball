<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/dashboard');
    exit;
}

$postId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($postId < 1) {
    setFlash('error', 'Invalid post ID.');
    header('Location: /admin/dashboard');
    exit;
}

try {
    // CASCADE DELETE: post_translations deleted automatically via FK
    $stmt = db()->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->execute([':id' => $postId]);
    setFlash('success', 'تم حذف المنشور بنجاح');
} catch (Throwable $e) {
    setFlash('error', 'Delete failed: ' . $e->getMessage());
}

header('Location: /admin/dashboard');
exit;
