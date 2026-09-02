#!/usr/bin/env php
<?php
/**
 * Minify frontend CSS/JS and write content-hashed files to frontend/dist/.
 * Run from project root: php scripts/build-assets.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$frontend = $root . '/frontend';
$dist = $frontend . '/dist';

if (!is_dir($dist) && !mkdir($dist, 0755, true) && !is_dir($dist)) {
    fwrite(STDERR, "Cannot create dist directory.\n");
    exit(1);
}

function minify_css(string $css): string {
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    $css = preg_replace('/\s+/', ' ', $css);
    return trim(str_replace([' {', '{ ', ' }', '} ', ': ', ' ;', '; ', ', '], ['{', '{', '}', '}', ':', ';', ';', ','], $css));
}

function minify_js(string $js): string {
    // Conservative minify: strip block/line comments (not inside strings) and excess whitespace
    $js = preg_replace('#/\*[\s\S]*?\*/#', '', $js);
    $js = preg_replace('#^\s*//.*$#m', '', $js);
    $js = preg_replace('/\n\s*\n/', "\n", $js);
    return trim($js);
}

$assets = [
    'style.css' => ['type' => 'css', 'minifier' => 'minify_css'],
    'shared.js' => ['type' => 'js', 'minifier' => 'minify_js'],
    'app.js' => ['type' => 'js', 'minifier' => 'minify_js'],
    'product.js' => ['type' => 'js', 'minifier' => 'minify_js'],
];

$manifest = [];

foreach ($assets as $logical => $cfg) {
    $source = $frontend . '/' . $logical;
    if (!is_readable($source)) {
        fwrite(STDERR, "Skip missing: {$logical}\n");
        continue;
    }
    $raw = file_get_contents($source);
    $min = $cfg['minifier']($raw);
    $hash = substr(hash('sha256', $min), 0, 10);
    $base = pathinfo($logical, PATHINFO_FILENAME);
    $ext = pathinfo($logical, PATHINFO_EXTENSION);
    $outName = "{$base}.{$hash}.{$ext}";
    $outPath = $dist . '/' . $outName;
    file_put_contents($outPath, $min);
    $manifest[$logical] = [
        'file' => $outName,
        'hash' => $hash,
        'built' => date('c'),
    ];
    echo "Built {$logical} → dist/{$outName}\n";
}

file_put_contents(
    $frontend . '/assets-manifest.json',
    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
);
echo "Wrote frontend/assets-manifest.json\n";
