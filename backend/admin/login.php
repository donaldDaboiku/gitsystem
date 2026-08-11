<?php
require_once __DIR__ . '/../config.php';
send_security_headers();

if (isLoggedIn()) {
    redirect(ADMIN_URL . '/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? null)) {
        $error = 'Invalid security token. Please refresh and try again.';
    } elseif (!rate_limit_allow('admin_login', LOGIN_MAX_ATTEMPTS, LOGIN_WINDOW_SECONDS)) {
        $error = 'Too many login attempts. Please try again in 15 minutes.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = ? AND is_active = 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                rate_limit_reset('admin_login');
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['_initiated'] = true;

                $stmt = $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?');
                $stmt->execute([$admin['id']]);

                logActivity($pdo, $admin['id'], 'login', 'Admin logged in');
                redirect(ADMIN_URL . '/index.php');
            } else {
                $error = 'Invalid username or password.';
                usleep(300000);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - GITSYSTEM</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <h1>GITSYSTEM</h1>
            <p>Admin Panel Login</p>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
