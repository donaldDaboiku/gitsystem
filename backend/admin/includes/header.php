<?php
require_once dirname(__DIR__, 2) . '/config.php';
requireLogin();

if (!defined('CSP_NONCE')) {
    define('CSP_NONCE', base64_encode(random_bytes(16)));
}
send_security_headers(false, CSP_NONCE);

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> - GITSYSTEM</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">GITSYSTEM</div>
            <nav>
                <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Dashboard</a>
                <a href="products.php" class="<?= $currentPage === 'products' ? 'active' : '' ?>">Products</a>
                <a href="inquiries.php" class="<?= $currentPage === 'inquiries' ? 'active' : '' ?>">Inquiries</a>
                <a href="settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">Settings</a>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <h1><?= htmlspecialchars($pageTitle ?? 'Admin') ?></h1>
                <span class="user-badge"><?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?></span>
            </header>
            <div class="content-area">
