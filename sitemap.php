<?php
/**
 * Dynamic sitemap for GITSYSTEM
 * Replace SITE_URL via backend config when live.
 */
require_once __DIR__ . '/backend/config.php';

header('Content-Type: application/xml; charset=utf-8');

$base = rtrim(SITE_URL, '/');
$frontend = $base . '/frontend';

$urls = [
    ['loc' => $frontend . '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => $frontend . '/index.html', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => $frontend . '/#software', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => $frontend . '/#products', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => $frontend . '/#contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
];

try {
    $stmt = $pdo->query("
        SELECT slug, updated_at, is_external_app, learn_url
        FROM products
        WHERE is_active = 1
        ORDER BY updated_at DESC
    ");
    foreach ($stmt->fetchAll() as $row) {
        if (!empty($row['is_external_app']) && !empty($row['learn_url'])) {
            continue; // external apps live off-site
        }
        $urls[] = [
            'loc' => $frontend . '/product/' . rawurlencode($row['slug']),
            'priority' => '0.7',
            'changefreq' => 'weekly',
            'lastmod' => date('Y-m-d', strtotime($row['updated_at'] ?? 'now')),
        ];
    }
} catch (Throwable $e) {
    // still emit core URLs
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= htmlspecialchars($u['loc'], ENT_XML1) ?></loc>
<?php if (!empty($u['lastmod'])): ?>
    <lastmod><?= htmlspecialchars($u['lastmod'], ENT_XML1) ?></lastmod>
<?php endif; ?>
    <changefreq><?= htmlspecialchars($u['changefreq'], ENT_XML1) ?></changefreq>
    <priority><?= htmlspecialchars($u['priority'], ENT_XML1) ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
