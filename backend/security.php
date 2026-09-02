<?php
/**
 * Security helpers: session hardening, CSRF, rate limiting, headers, mail.
 * Loaded from config.php before session_start().
 */

if (!defined('RATE_LIMIT_DIR')) {
    define('RATE_LIMIT_DIR', __DIR__ . '/storage/ratelimit');
}

/**
 * Apply secure session cookie settings before session_start().
 */
function hardenSession(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (defined('FORCE_SECURE_COOKIES') && FORCE_SECURE_COOKIES);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', $secure ? '1' : '0');
    ini_set('session.gc_maxlifetime', (string)(defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 7200));

    session_name('GITSYSTEMSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(?string $token): bool {
    if ($token === null || $token === '' || empty($_SESSION['_csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['_csrf_token'], $token);
}

function require_csrf_from_post(): void {
    $token = $_POST['_csrf'] ?? '';
    if (!verify_csrf($token)) {
        http_response_code(403);
        die('Invalid security token. Please refresh and try again.');
    }
}

function client_ip(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * File-based rate limiter. Returns true if allowed, false if blocked.
 */
function rate_limit_allow(string $bucket, int $maxAttempts, int $windowSeconds): bool {
    if (!is_dir(RATE_LIMIT_DIR)) {
        @mkdir(RATE_LIMIT_DIR, 0755, true);
    }

    $key = hash('sha256', $bucket . '|' . client_ip());
    $file = RATE_LIMIT_DIR . '/' . $key . '.json';
    $now = time();
    $data = ['attempts' => [], 'blocked_until' => 0];

    if (is_readable($file)) {
        $raw = json_decode((string)file_get_contents($file), true);
        if (is_array($raw)) {
            $data = array_merge($data, $raw);
        }
    }

    if (!empty($data['blocked_until']) && $now < (int)$data['blocked_until']) {
        return false;
    }

    $cutoff = $now - $windowSeconds;
    $attempts = array_values(array_filter($data['attempts'] ?? [], static function ($t) use ($cutoff) {
        return (int)$t >= $cutoff;
    }));

    if (count($attempts) >= $maxAttempts) {
        $data['attempts'] = $attempts;
        $data['blocked_until'] = $now + $windowSeconds;
        file_put_contents($file, json_encode($data), LOCK_EX);
        return false;
    }

    $attempts[] = $now;
    $data['attempts'] = $attempts;
    $data['blocked_until'] = 0;
    file_put_contents($file, json_encode($data), LOCK_EX);
    return true;
}

function rate_limit_reset(string $bucket): void {
    $key = hash('sha256', $bucket . '|' . client_ip());
    $file = RATE_LIMIT_DIR . '/' . $key . '.json';
    if (is_file($file)) {
        @unlink($file);
    }
}

function send_security_headers(bool $jsonApi = false, ?string $cspNonce = null): void {
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    $scriptSrc = "'self'";
    if ($cspNonce) {
        $scriptSrc .= " 'nonce-" . $cspNonce . "'";
    }

    header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src {$scriptSrc}; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'");

    if (!$jsonApi) {
        header('X-XSS-Protection: 0');
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

/**
 * Notify site inbox about a new inquiry via SMTP (PHPMailer) or mail() fallback.
 */
function notify_inquiry_email(PDO $pdo, array $inquiry): bool {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_email' LIMIT 1");
    $stmt->execute();
    $to = trim((string)$stmt->fetchColumn());
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $name = $inquiry['name'] ?? 'Visitor';
    $email = $inquiry['email'] ?? '';
    $phone = $inquiry['phone'] ?? '';
    $service = $inquiry['service'] ?? '';
    $message = $inquiry['message'] ?? '';
    $productId = $inquiry['product_id'] ?? null;

    $subject = 'New website inquiry from ' . $name;
    $body = "New inquiry received via GITSYSTEM website\n\n"
        . "Name: {$name}\n"
        . "Email: {$email}\n"
        . "Phone: {$phone}\n"
        . "Service: {$service}\n"
        . "Product ID: " . ($productId ?: '—') . "\n\n"
        . "Message:\n{$message}\n\n"
        . "IP: " . client_ip() . "\n"
        . "Time: " . date('Y-m-d H:i:s') . "\n";

    $from = defined('MAIL_FROM') && MAIL_FROM
        ? MAIL_FROM
        : ('noreply@' . (parse_url(SITE_URL, PHP_URL_HOST) ?: 'gitsystemng.com'));

    $autoload = __DIR__ . '/vendor/autoload.php';
    if (defined('SMTP_HOST') && SMTP_HOST && is_readable($autoload)) {
        require_once $autoload;
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->Port = (int)SMTP_PORT;
            $mail->SMTPAuth = SMTP_USER !== '';
            if ($mail->SMTPAuth) {
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
            }
            if (SMTP_SECURE === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } elseif (SMTP_SECURE === 'tls') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($from, 'GITSYSTEM');
            $mail->addAddress($to);
            $mail->addReplyTo($email, $name);
            $mail->Subject = $subject;
            $mail->Body = $body;
            return $mail->send();
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                error_log('SMTP inquiry mail failed: ' . $e->getMessage());
            }
            return false;
        }
    }

    $headers = [
        'From: GITSYSTEM <' . $from . '>',
        'Reply-To: ' . $email,
        'X-Mailer: GITSYSTEM',
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
