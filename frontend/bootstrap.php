<?php
/**
 * Frontend bootstrap — loads config for SITE_URL and asset helpers.
 */
require_once __DIR__ . '/../backend/config.php';
send_security_headers();

function frontend_url(string $path = ''): string {
    $base = rtrim(SITE_URL, '/');
    $path = ltrim($path, '/');
    if ($path === '' || $path === 'index.php' || $path === 'index.html') {
        return $base . '/';
    }
    return $base . '/frontend/' . $path;
}

function frontend_h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Resolve built asset path from manifest (fallback to source file).
 */
function asset_url(string $logicalName): string {
    static $manifest = null;
    if ($manifest === null) {
        $file = __DIR__ . '/assets-manifest.json';
        $manifest = is_readable($file)
            ? (json_decode((string)file_get_contents($file), true) ?: [])
            : [];
    }
    $entry = $manifest[$logicalName] ?? null;
    $rel = ($entry && !empty($entry['file'])) ? 'dist/' . $entry['file'] : $logicalName;
    return '/frontend/' . ltrim($rel, '/');
}

function asset_tag_css(string $logicalName): string {
    return '<link rel="stylesheet" href="' . frontend_h(asset_url($logicalName)) . '">';
}

function asset_tag_js(string $logicalName): string {
    return '<script src="' . frontend_h(asset_url($logicalName)) . '"></script>';
}
