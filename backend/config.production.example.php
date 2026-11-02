<?php
/**
 * Copy these values into backend/config.php ON THE SERVER
 * (or create them as defines before the defaults).
 * DO NOT commit real passwords to git.
 *
 * Domain: gitsystemng.com
 */

// From Namecheap cPanel → MySQL Databases (use the FULL names shown there)
define('DB_HOST', 'localhost');
define('DB_NAME', 'REPLACE_WITH_CPANEL_DB_NAME');      // e.g. gitsys_gitsystemng
define('DB_USER', 'REPLACE_WITH_CPANEL_DB_USER');      // e.g. gitsys_admin
define('DB_PASS', 'REPLACE_WITH_CPANEL_DB_PASSWORD');

define('SITE_URL', 'https://gitsystemng.com');
define('WEB_BASE_PATH', '/backend');
define('ADMIN_PATH', WEB_BASE_PATH . '/admin');
define('UPLOAD_WEB_PATH', WEB_BASE_PATH . '/uploads');

define('APP_DEBUG', false); // never true on the live server
define('MAIL_FROM', 'noreply@gitsystemng.com');

// Optional SMTP (Namecheap email or another SMTP provider)
// define('SMTP_HOST', 'mail.gitsystemng.com');
// define('SMTP_PORT', 587);
// define('SMTP_USER', 'noreply@gitsystemng.com');
// define('SMTP_PASS', 'your-email-password');
// define('SMTP_SECURE', 'tls');
