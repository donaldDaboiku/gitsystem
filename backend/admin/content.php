<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../content_store.php';
requireLogin();

$kinds = content_kinds();
$kind = $_GET['kind'] ?? $_POST['kind'] ?? 'service';
if (!isset($kinds[$kind])) {
    $kind = 'service';
}
$pageTitle = $kinds[$kind];
$message = '';
$error = '';

try {
    content_ensure($pdo);
} catch (PDOException $e) {
    $error = APP_DEBUG ? $e->getMessage() : 'The content tables could not be created.';
}

if ($error === '' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    require_csrf_from_post();
    $id = (int)$_POST['delete_id'];
    $pdo->prepare('DELETE FROM content_items WHERE id = ? AND kind = ?')->execute([$id, $kind]);
    logActivity($pdo, $_SESSION['admin_id'], 'delete_content', "Deleted $kind #$id");
    redirect('content.php?kind=' . urlencode($kind) . '&msg=deleted');
}

if ($error === '' && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    require_csrf_from_post();
    $id = (int)($_POST['id'] ?? 0);
    $title = trim(strip_tags($_POST['title'] ?? ''));
    $body = trim(strip_tags($_POST['body'] ?? ''));
    $sort = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    if ($title === '' || $body === '') {
        $error = 'Title and text are required.';
    } else {
        if ($id > 0) {
            $pdo->prepare('UPDATE content_items SET title=?, body=?, sort_order=?, is_active=? WHERE id=? AND kind=?')
                ->execute([$title, $body, $sort, $active, $id, $kind]);
            $message = 'Saved.';
        } else {
            $pdo->prepare('INSERT INTO content_items (kind, title, body, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')
                ->execute([$kind, $title, $body, $sort, $active]);
            $message = 'Added.';
        }
        logActivity($pdo, $_SESSION['admin_id'], 'save_content', "Saved $kind: $title");
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Deleted.';
}

$edit = null;
$items = [];
if ($error === '') {
    if (!empty($_GET['edit'])) {
        $stmt = $pdo->prepare('SELECT * FROM content_items WHERE id = ? AND kind = ?');
        $stmt->execute([(int)$_GET['edit'], $kind]);
        $edit = $stmt->fetch() ?: null;
    }
    $stmt = $pdo->prepare('SELECT * FROM content_items WHERE kind = ? ORDER BY sort_order, id');
    $stmt->execute([$kind]);
    $items = $stmt->fetchAll();
}
$nameLabel = $kind === 'testimonial' ? 'Name' : 'Title';
$textLabel = $kind === 'testimonial' ? 'Quote' : 'Text';

require __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="filter-row">
    <?php foreach ($kinds as $key => $label): ?>
        <a class="btn btn-sm <?= $kind === $key ? 'btn-primary' : 'btn-muted' ?>" href="content.php?kind=<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2><?= $edit ? 'Edit' : 'Add' ?> <?= htmlspecialchars($pageTitle) ?></h2>
    <?php if ($kind === 'testimonial'): ?>
        <p class="text-muted-sm">Leave this empty until you have a real quote. Nothing is shown on the site while this list is empty.</p>
    <?php endif; ?>
    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="kind" value="<?= htmlspecialchars($kind) ?>">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <div class="form-group">
            <label for="title"><?= $nameLabel ?></label>
            <input type="text" id="title" name="title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="body"><?= $textLabel ?></label>
            <textarea id="body" name="body" required><?= htmlspecialchars($edit['body'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Order</label>
                <input type="number" id="sort_order" name="sort_order" value="<?= (int)($edit['sort_order'] ?? (count($items) + 1)) ?>">
            </div>
            <div class="form-group">
                <label for="is_active">Shown on the site</label>
                <input type="checkbox" id="is_active" name="is_active" value="1" <?= !isset($edit['is_active']) || (int)$edit['is_active'] === 1 ? 'checked' : '' ?>>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <?php if ($edit): ?><a class="btn btn-muted" href="content.php?kind=<?= htmlspecialchars($kind) ?>">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="card">
    <h2><?= htmlspecialchars($pageTitle) ?> (<?= count($items) ?>)</h2>
    <?php if (!$items): ?>
        <p class="text-muted">None yet.</p>
    <?php else: ?>
        <table>
            <thead><tr><th><?= $nameLabel ?></th><th><?= $textLabel ?></th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr class="<?= (int)$item['is_active'] === 1 ? '' : 'product-not-live' ?>">
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= htmlspecialchars($item['body']) ?></td>
                    <td class="actions">
                        <a class="btn btn-sm btn-primary" href="content.php?kind=<?= htmlspecialchars($kind) ?>&amp;edit=<?= (int)$item['id'] ?>">Edit</a>
                        <form method="post" class="form-inline" onsubmit="return confirm('Delete this item?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="kind" value="<?= htmlspecialchars($kind) ?>">
                            <input type="hidden" name="delete_id" value="<?= (int)$item['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
