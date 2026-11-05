<?php
require __DIR__ . '/../backend/config.php';
$rows = $pdo->query('SELECT username, email, full_name, is_active FROM admin_users')->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['username'] . "\t" . $r['email'] . "\t" . $r['full_name'] . "\tactive=" . $r['is_active'] . PHP_EOL;
}
$hash = $pdo->query("SELECT password FROM admin_users WHERE username = 'admin'")->fetchColumn();
echo 'default_password_matches=' . (password_verify('admin123', (string) $hash) ? 'yes' : 'no') . PHP_EOL;
