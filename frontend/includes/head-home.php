<?php
/** Homepage OG / SEO meta — requires bootstrap.php */
$canonical = frontend_url();
$ogImage = frontend_url('assets/gitsystem-logo.png');
$metaDesc = 'Professional IT solutions, hardware/software services, networking, tech academy, and consultation in Nigeria. Your trusted technology partner.';
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GITSYSTEM - Greenpasture Integrated Technology</title>
    <meta name="description" content="<?= frontend_h($metaDesc) ?>">
    <link rel="icon" href="/frontend/assets/gitsystem-icon.png" type="image/png">
    <link rel="canonical" href="<?= frontend_h($canonical) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="GITSYSTEM">
    <meta property="og:title" content="GITSYSTEM — Greenpasture Integrated Technology">
    <meta property="og:description" content="<?= frontend_h($metaDesc) ?>">
    <meta property="og:url" content="<?= frontend_h($canonical) ?>">
    <meta property="og:image" content="<?= frontend_h($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="GITSYSTEM — Greenpasture Integrated Technology">
    <meta name="twitter:description" content="<?= frontend_h($metaDesc) ?>">
    <meta name="twitter:image" content="<?= frontend_h($ogImage) ?>">
    <meta name="api-base" content="/backend/data.php">
    <meta name="frontend-base" content="/frontend">
    <meta name="gitinvoice-url" content="https://git-invoice-three.vercel.app">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?= asset_tag_css('style.css') ?>
