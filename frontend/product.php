<?php
require_once __DIR__ . '/bootstrap.php';
$navCurrent = 'products';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require __DIR__ . '/includes/head-product.php'; ?>
</head>
<body class="product-page">
    <div class="bg-animation">
        <div class="grid-overlay"></div>
        <div class="glow-orb orb-1"></div>
        <div class="glow-orb orb-2"></div>
    </div>

    <?php require __DIR__ . '/includes/nav.php'; ?>

    <main id="productMain">
        <div class="product-loading-screen">
            <div class="loader-ring"></div>
            <p>Loading product…</p>
        </div>
    </main>

    <div class="product-sticky-bar" id="stickyBar" hidden>
        <div class="sticky-bar-inner">
            <div class="sticky-info">
                <span class="sticky-name" id="stickyName"></span>
                <span class="sticky-price" id="stickyPrice"></span>
            </div>
            <div class="sticky-actions">
                <button type="button" class="btn btn-secondary btn-sm" id="stickyContact">Contact Us</button>
                <button type="button" class="btn btn-primary btn-sm" id="stickyBuy">Request Quote</button>
            </div>
        </div>
    </div>

    <footer class="product-footer-mini">
        <p>&copy; <span id="currentYear"></span> GITSYSTEM — <a href="/">Back to home</a></p>
    </footer>

    <button class="scroll-top" id="scrollTop">↑</button>

    <?= asset_tag_js('shared.js') ?>
    <?= asset_tag_js('product.js') ?>
</body>
</html>
