<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$pageTitle = 'Site Settings';
$message = '';
$error = '';
$passwordMessage = '';
$passwordError = '';

$keys = [
    'site_name', 'site_email', 'site_phone', 'site_address',
    'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'whatsapp_number'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_from_post();
    $formAction = $_POST['form_action'] ?? 'site_settings';

    if ($formAction === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            $passwordError = 'All password fields are required.';
        } elseif (strlen($new) < 8) {
            $passwordError = 'New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $passwordError = 'New password and confirmation do not match.';
        } else {
            $stmt = $pdo->prepare('SELECT password FROM admin_users WHERE id = ? AND is_active = 1');
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($current, $admin['password'])) {
                $passwordError = 'Current password is incorrect.';
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE admin_users SET password = ? WHERE id = ?');
                $stmt->execute([$hash, $_SESSION['admin_id']]);
                logActivity($pdo, $_SESSION['admin_id'], 'change_password', 'Admin changed password');
                $passwordMessage = 'Password updated successfully.';
            }
        }
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    $stmt->execute([$key, sanitize($_POST[$key])]);
                }
            }
            logActivity($pdo, $_SESSION['admin_id'], 'update_settings', 'Updated site contact settings');
            $message = 'Settings saved. Changes appear on the website immediately.';
        } catch (PDOException $e) {
            $error = 'Failed to save settings.';
        }
    }
}

$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

function setting($key, $settings, $default = '') {
    return htmlspecialchars($settings[$key] ?? $default);
}

require __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <h2>Contact &amp; Business Info</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
        These details are stored in the database and shown on your website contact section and footer.
    </p>
    <form method="POST">
        <input type="hidden" name="form_action" value="site_settings">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label for="site_name">Site / Business Name</label>
                <input type="text" id="site_name" name="site_name" value="<?= setting('site_name', $settings, 'GITSYSTEM') ?>">
            </div>
            <div class="form-group">
                <label for="site_email">Email Address</label>
                <input type="email" id="site_email" name="site_email" value="<?= setting('site_email', $settings) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="site_phone">Phone Number</label>
                <input type="text" id="site_phone" name="site_phone" value="<?= setting('site_phone', $settings) ?>" placeholder="+234 800 000 0000">
            </div>
            <div class="form-group">
                <label for="whatsapp_number">WhatsApp Number</label>
                <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?= setting('whatsapp_number', $settings) ?>" placeholder="+2348000000000">
            </div>
        </div>
        <div class="form-group">
            <label for="site_address">Address</label>
            <input type="text" id="site_address" name="site_address" value="<?= setting('site_address', $settings) ?>">
        </div>
        <h3 style="margin: 1.5rem 0 1rem; color: var(--primary); font-size: 1rem;">Social Links</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="facebook_url">Facebook URL</label>
                <input type="url" id="facebook_url" name="facebook_url" value="<?= setting('facebook_url', $settings) ?>">
            </div>
            <div class="form-group">
                <label for="instagram_url">Instagram URL</label>
                <input type="url" id="instagram_url" name="instagram_url" value="<?= setting('instagram_url', $settings) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="twitter_url">Twitter / X URL</label>
                <input type="url" id="twitter_url" name="twitter_url" value="<?= setting('twitter_url', $settings) ?>">
            </div>
            <div class="form-group">
                <label for="linkedin_url">LinkedIn URL</label>
                <input type="url" id="linkedin_url" name="linkedin_url" value="<?= setting('linkedin_url', $settings) ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

<div class="card" style="margin-top: 2rem;">
    <h2>Change Admin Password</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
        Update the password for <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></strong>.
        Use at least 8 characters.
    </p>

    <?php if ($passwordMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($passwordMessage) ?></div>
    <?php endif; ?>
    <?php if ($passwordError): ?>
        <div class="alert alert-error"><?= htmlspecialchars($passwordError) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <input type="hidden" name="form_action" value="change_password">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
