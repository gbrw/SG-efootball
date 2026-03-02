<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/dashboard.php');
    exit;
}

$postId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($postId < 1) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Invalid post ID.'];
    header('Location: /admin/dashboard.php');
    exit;
}

try {
    // CASCADE DELETE: post_translations deleted automatically via FK
    $stmt = db()->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->execute([':id' => $postId]);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'تم حذف المنشور بنجاح'];
} catch (Throwable $e) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Delete failed: ' . $e->getMessage()];
}

header('Location: /admin/dashboard.php');
exit;
