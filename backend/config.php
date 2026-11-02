<?php
// Load local overrides first (for Laragon / local testing)
if (is_readable(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// Database — set real values in config.local.php (local) or on the server only.
// NEVER commit real passwords or cPanel DB names into this file.
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'gitsystemng');
if (!defined('DB_USER')) define('DB_USER', 'your_db_username');
if (!defined('DB_PASS')) define('DB_PASS', 'your_db_password');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// Site — override in config.local.php (local) or edit on the server (production)
if (!defined('SITE_URL')) define('SITE_URL', 'https://yourdomain.com');
if (!defined('WEB_BASE_PATH')) define('WEB_BASE_PATH', '/backend');
if (!defined('ADMIN_PATH')) define('ADMIN_PATH', WEB_BASE_PATH . '/admin');
if (!defined('UPLOAD_WEB_PATH')) define('UPLOAD_WEB_PATH', WEB_BASE_PATH . '/uploads');
if (!defined('MAIL_FROM')) define('MAIL_FROM', '');
if (!defined('SMTP_HOST')) define('SMTP_HOST', '');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', '');
if (!defined('SMTP_PASS')) define('SMTP_PASS', '');
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', 'tls'); // tls | ssl | empty
if (!defined('IMAGE_MAX_WIDTH')) define('IMAGE_MAX_WIDTH', 1600);

define('ADMIN_URL', SITE_URL . ADMIN_PATH);
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . UPLOAD_WEB_PATH . '/');

// Security
define('SESSION_LIFETIME', 3600 * 2);  // 2 hours
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_WINDOW_SECONDS', 900);      // 15 minutes
define('INQUIRY_MAX_ATTEMPTS', 5);
define('INQUIRY_WINDOW_SECONDS', 3600);   // 1 hour

// Timezone
date_default_timezone_set('Africa/Lagos');

// Error Reporting — keep false in this file; enable only via config.local.php for local/dev
if (!defined('APP_DEBUG')) define('APP_DEBUG', false);
error_reporting(APP_DEBUG ? E_ALL : 0);
ini_set('display_errors', APP_DEBUG ? '1' : '0');

require_once __DIR__ . '/security.php';

// Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    if (APP_DEBUG) {
        die('Database Connection Failed: ' . $e->getMessage());
    }
    http_response_code(503);
    die('Service temporarily unavailable.');
}

// Helper Functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect(ADMIN_URL . '/login.php');
    }
}

function logActivity($pdo, $admin_id, $action, $description = '') {
    $ip = client_ip();
    $stmt = $pdo->prepare('INSERT INTO activity_log (admin_id, action, description, ip_address) VALUES (?, ?, ?, ?)');
    $stmt->execute([$admin_id, $action, $description, $ip]);
}

function uploadImage($file, $folder = 'products') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, WebP, and GIF allowed'];
    }

    $uploadDir = UPLOAD_PATH . $folder . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($extension, $allowedExt, true)) {
        return ['success' => false, 'message' => 'Invalid file extension'];
    }

    $basename = bin2hex(random_bytes(8)) . '_' . time();
    $filename = $basename . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }

    $relativePath = $folder . '/' . $filename;
    $webpPath = processUploadedImage($filepath, $mimeType, $uploadDir, $basename);

    return [
        'success' => true,
        'filename' => $filename,
        'path' => $relativePath,
        'webp_path' => $webpPath,
        'url' => UPLOAD_URL . $relativePath,
        'webp_url' => $webpPath ? UPLOAD_URL . $webpPath : null,
    ];
}

/**
 * Resize original (max width) and write WebP variant. Returns relative webp path or null.
 */
function processUploadedImage(string $filepath, string $mimeType, string $uploadDir, string $basename): ?string {
    if (!extension_loaded('gd')) {
        return null;
    }

    $image = loadGdImage($filepath, $mimeType);
    if (!$image) {
        return null;
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $maxW = (int)IMAGE_MAX_WIDTH;

    if ($width > $maxW) {
        $newW = $maxW;
        $newH = (int)round($height * ($maxW / $width));
        $resized = imagescale($image, $newW, $newH, IMG_BILINEAR_FIXED);
        if ($resized) {
            imagedestroy($image);
            $image = $resized;
            saveGdImage($image, $filepath, $mimeType);
        }
    }

    $webpPath = null;
    if (function_exists('imagewebp')) {
        $webpFile = $uploadDir . $basename . '.webp';
        if (@imagewebp($image, $webpFile, 82)) {
            $folder = trim(str_replace('\\', '/', str_replace(rtrim(UPLOAD_PATH, '/\\') . DIRECTORY_SEPARATOR, '', $uploadDir)), '/\\');
            $webpPath = ($folder !== '' ? $folder . '/' : '') . $basename . '.webp';
        }
    }

    imagedestroy($image);
    return $webpPath;
}

function loadGdImage(string $path, string $mimeType) {
    return match ($mimeType) {
        'image/jpeg' => @imagecreatefromjpeg($path),
        'image/png' => @imagecreatefrompng($path),
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        'image/gif' => @imagecreatefromgif($path),
        default => false,
    };
}

function saveGdImage($image, string $path, string $mimeType): bool {
    return match ($mimeType) {
        'image/jpeg' => @imagejpeg($image, $path, 85),
        'image/png' => @imagepng($image, $path, 6),
        'image/webp' => function_exists('imagewebp') ? @imagewebp($image, $path, 82) : false,
        'image/gif' => @imagegif($image, $path),
        default => false,
    };
}

function image_webp_path(?string $relativePath, ?string $storedWebp = null): ?string {
    if ($storedWebp) {
        return $storedWebp;
    }
    if (!$relativePath) {
        return null;
    }
    $webp = preg_replace('/\.[^.]+$/', '.webp', $relativePath);
    return ($webp && file_exists(UPLOAD_PATH . $webp)) ? $webp : null;
}

function format_image_urls(?string $path, ?string $webpPath = null): array {
    if (!$path) {
        return ['url' => null, 'webp_url' => null];
    }
    $webp = image_webp_path($path, $webpPath);
    return [
        'url' => UPLOAD_URL . $path,
        'webp_url' => $webp ? UPLOAD_URL . $webp : null,
    ];
}

function deleteImage($path, $webpPath = null) {
    $fullPath = UPLOAD_PATH . $path;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
    $webp = image_webp_path($path, $webpPath);
    if ($webp) {
        $webpFull = UPLOAD_PATH . $webp;
        if (file_exists($webpFull)) {
            unlink($webpFull);
        }
    }
    return true;
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function formatPrice($price) {
    return '₦' . number_format($price, 2);
}

// Hardened session
hardenSession();
session_start();

if (isLoggedIn() && empty($_SESSION['_initiated'])) {
    session_regenerate_id(true);
    $_SESSION['_initiated'] = true;
}
