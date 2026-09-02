<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$pageTitle = 'Inquiries';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_id'], $_POST['status'])) {
    require_csrf_from_post();
    $id = (int)$_POST['inquiry_id'];
    $status = sanitize($_POST['status']);
    $allowed = ['new', 'contacted', 'resolved', 'spam'];

    if (in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        logActivity($pdo, $_SESSION['admin_id'], 'update_inquiry', "Updated inquiry #$id to $status");
        $message = 'Inquiry status updated.';
    }
}

$filter = $_GET['status'] ?? 'all';
$sql = "SELECT i.*, p.name as product_name FROM inquiries i LEFT JOIN products p ON i.product_id = p.id";
$params = [];

if ($filter !== 'all') {
    $sql .= " WHERE i.status = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY i.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card">
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
        <?php foreach (['all' => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'resolved' => 'Resolved', 'spam' => 'Spam'] as $key => $label): ?>
            <a href="?status=<?= $key ?>" class="btn btn-sm <?= $filter === $key ? 'btn-primary' : '' ?>" style="<?= $filter !== $key ? 'background: var(--dark); color: var(--text-muted); border: 1px solid var(--border);' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($inquiries)): ?>
        <p style="color: var(--text-muted);">No inquiries found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Service</th>
                    <th>Product</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiries as $inq): ?>
                <tr>
                    <td><?= htmlspecialchars($inq['name']) ?></td>
                    <td>
                        <?= htmlspecialchars($inq['email']) ?><br>
                        <small style="color: var(--text-muted);"><?= htmlspecialchars($inq['phone'] ?: '') ?></small>
                    </td>
                    <td><?= htmlspecialchars($inq['service'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($inq['product_name'] ?: '—') ?></td>
                    <td style="max-width: 200px;"><?= htmlspecialchars(mb_substr($inq['message'], 0, 80)) ?><?= mb_strlen($inq['message']) > 80 ? '…' : '' ?></td>
                    <td><span class="badge badge-<?= htmlspecialchars($inq['status']) ?>"><?= htmlspecialchars(ucfirst($inq['status'])) ?></span></td>
                    <td><?= date('M j, Y', strtotime($inq['created_at'])) ?></td>
                    <td>
                        <form method="POST" class="form-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="inquiry_id" value="<?= (int)$inq['id'] ?>">
                            <select name="status" data-auto-submit class="select-compact">
                                <?php foreach (['new', 'contacted', 'resolved', 'spam'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $inq['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
