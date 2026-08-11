<?php
require_once __DIR__ . '/../config.php';

if (isset($_SESSION['admin_id'])) {
    logActivity($pdo, $_SESSION['admin_id'], 'logout', 'Admin logged out');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'], $params['httponly']);
}
session_destroy();

header('Location: login.php');
exit;
