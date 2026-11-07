<?php
/** Inner page head. Set $pageTitle and $pageDescription before including. Requires bootstrap.php. */
$pageTitle = $pageTitle ?? 'GITSYSTEM';
$pageDescription = $pageDescription ?? 'GreenPasture Integrated Tech System.';
if (!empty($seoKey)) {
    $seo = seo_public($pdo, (string)$seoKey);
    if ($seo) {
        if ($seo['title'] !== '') $pageTitle = $seo['title'];
        if ($seo['description'] !== '') $pageDescription = $seo['description'];
    }
}
$canonical = rtrim(SITE_URL, '/') . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= frontend_h($pageTitle) ?> — GITSYSTEM</title>
    <meta name="description" content="<?= frontend_h($pageDescription) ?>">
    <link rel="icon" href="/frontend/assets/gitsystem-icon.png" type="image/png">
    <link rel="canonical" href="<?= frontend_h($canonical) ?>">
    <meta name="api-base" content="/backend/data.php">
    <meta name="frontend-base" content="/frontend">
    <meta name="gitinvoice-url" content="https://git-invoice-three.vercel.app">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <?= asset_tag_css('style.css') ?>
