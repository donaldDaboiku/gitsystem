<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../content_store.php';
requireLogin();

$pageTitle = 'SEO';
$message = '';
$error = '';

try {
    content_ensure($pdo);
} catch (PDOException $e) {
    $error = APP_DEBUG ? $e->getMessage() : 'The content tables could not be created.';
}

if ($error === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_from_post();
    $stmt = $pdo->prepare('INSERT INTO page_seo (page_key, title, description) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description)');
    foreach (seo_defaults() as $key => $unused) {
        $title = trim(strip_tags($_POST['title'][$key] ?? ''));
        $description = trim(strip_tags($_POST['description'][$key] ?? ''));
        if ($title === '' || $description === '') {
            $error = 'Every page needs a title and a description.';
            break;
        }
        if (strlen($description) > 300) {
            $description = substr($description, 0, 300);
        }
        $stmt->execute([$key, $title, $description]);
    }
    if ($error === '') {
        logActivity($pdo, $_SESSION['admin_id'], 'save_seo', 'Updated page titles');
        $message = 'Saved.';
    }
}

$rows = [];
if ($error === '') {
    foreach ($pdo->query('SELECT page_key, title, description FROM page_seo')->fetchAll() as $row) {
        $rows[$row['page_key']] = $row;
    }
}

require __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <h2>Page titles</h2>
    <p class="text-muted-sm">These are the browser title and the short description search engines read. The headings on the page stay as they are.</p>
    <form method="post">
        <?= csrf_field() ?>
        <?php foreach (seo_defaults() as $key => [$fallbackTitle, $fallbackDesc]):
            $title = $rows[$key]['title'] ?? $fallbackTitle;
            $description = $rows[$key]['description'] ?? $fallbackDesc;
        ?>
            <div class="form-group">
                <label for="title-<?= htmlspecialchars($key) ?>"><?= htmlspecialchars(ucfirst($key)) ?> title</label>
                <input type="text" id="title-<?= htmlspecialchars($key) ?>" name="title[<?= htmlspecialchars($key) ?>]" required value="<?= htmlspecialchars($title) ?>">
            </div>
            <div class="form-group">
                <label for="desc-<?= htmlspecialchars($key) ?>">Description</label>
                <textarea id="desc-<?= htmlspecialchars($key) ?>" name="description[<?= htmlspecialchars($key) ?>]" required><?= htmlspecialchars($description) ?></textarea>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
