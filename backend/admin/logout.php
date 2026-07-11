<?php
require_once __DIR__ . '/../config.php';

if (isset($_SESSION['admin_id'])) {
    logActivity($pdo, $_SESSION['admin_id'], 'logout', 'Admin logged out');
}

session_unset();
session_destroy();
header('Location: login.php');
exit;
