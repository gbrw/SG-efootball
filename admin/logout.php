<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    adminLogout();
}

header('Location: /admin/login.php');
exit;
