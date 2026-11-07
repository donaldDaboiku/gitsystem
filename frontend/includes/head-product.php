<?php
/** Product page OG / SEO meta — requires bootstrap.php */
$canonical = frontend_url('product/' . ($_GET['slug'] ?? ''));
$ogImage = frontend_url('assets/gitsystem-logo.png');
$metaDesc = 'View product details, pricing, and request a quote from GITSYSTEM.';
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product — GITSYSTEM</title>
    <meta name="description" content="<?= frontend_h($metaDesc) ?>">
    <link rel="canonical" href="<?= frontend_h($canonical) ?>">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="GITSYSTEM">
    <meta property="og:title" content="Product — GITSYSTEM">
    <meta property="og:description" content="<?= frontend_h($metaDesc) ?>">
    <meta property="og:url" content="<?= frontend_h($canonical) ?>">
    <meta property="og:image" content="<?= frontend_h($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Product — GITSYSTEM">
    <meta name="twitter:description" content="<?= frontend_h($metaDesc) ?>">
    <meta name="twitter:image" content="<?= frontend_h($ogImage) ?>">
    <meta name="api-base" content="/backend/data.php">
    <meta name="frontend-base" content="/frontend">
    <meta name="gitinvoice-url" content="https://git-invoice-three.vercel.app">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="/frontend/assets/gitsystem-icon.png" type="image/png">
    <?= asset_tag_css('style.css') ?>
