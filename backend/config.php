<?php
// Load local overrides first (for Laragon / local testing)
if (is_readable(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// Database — overridden by config.local.php when present
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'gitsystemng');
if (!defined('DB_USER')) define('DB_USER', 'your_db_username');
if (!defined('DB_PASS')) define('DB_PASS', 'your_db_password');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// Site — Namecheap flat deploy: WEB_BASE_PATH '' and ADMIN_PATH '/admin'
if (!defined('SITE_URL')) define('SITE_URL', 'https://yourdomain.com');
if (!defined('WEB_BASE_PATH')) define('WEB_BASE_PATH', '');
if (!defined('ADMIN_PATH')) define('ADMIN_PATH', '/admin');
if (!defined('UPLOAD_WEB_PATH')) define('UPLOAD_WEB_PATH', WEB_BASE_PATH . '/uploads');

define('ADMIN_URL', SITE_URL . ADMIN_PATH);
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . UPLOAD_WEB_PATH . '/');

// Security
define('SESSION_LIFETIME', 3600 * 2);  // 2 hours
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Timezone
date_default_timezone_set('Africa/Lagos');

// Error Reporting — set APP_DEBUG to true during local development only
if (!defined('APP_DEBUG')) define('APP_DEBUG', false);
error_reporting(APP_DEBUG ? E_ALL : 0);
ini_set('display_errors', APP_DEBUG ? '1' : '0');

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
    die("Database Connection Failed: " . $e->getMessage());
}

// Helper Functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
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
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare("INSERT INTO activity_log (admin_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$admin_id, $action, $description, $ip]);
}

function uploadImage($file, $folder = 'products') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, WebP, and GIF allowed'];
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = UPLOAD_PATH . $folder . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $folder . '/' . $filename,
            'url' => UPLOAD_URL . $folder . '/' . $filename
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

function deleteImage($path) {
    $fullPath = UPLOAD_PATH . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
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

// Start session
session_start();
?>
