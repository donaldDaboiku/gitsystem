<?php
/**
 * PASTE INTO backend/config.php ON NAMECHEAP (File Manager → Edit).
 * Do not upload config.local.php. Do not commit real passwords to git.
 *
 * Domain: gitsystemng.com
 * Database (already created/imported): gitsrcgt_gitsystemng
 *
 * Fill DB_USER + DB_PASS from cPanel → MySQL Databases
 * (Current Users / Current Databases — use the FULL names).
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'gitsrcgt_gitsystemng');
define('DB_USER', 'gitsrcgt_PUT_YOUR_USER_HERE');   // e.g. gitsrcgt_admin
define('DB_PASS', 'PUT_YOUR_MYSQL_PASSWORD_HERE');

define('SITE_URL', 'https://gitsystemng.com');
define('WEB_BASE_PATH', '/backend');
define('ADMIN_PATH', WEB_BASE_PATH . '/admin');
define('UPLOAD_WEB_PATH', WEB_BASE_PATH . '/uploads');

define('APP_DEBUG', false);
define('MAIL_FROM', 'noreply@gitsystemng.com');

// Optional SMTP
// define('SMTP_HOST', 'mail.gitsystemng.com');
// define('SMTP_PORT', 587);
// define('SMTP_USER', 'noreply@gitsystemng.com');
// define('SMTP_PASS', 'your-email-password');
// define('SMTP_SECURE', 'tls');
