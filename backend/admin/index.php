<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$pageTitle = 'Dashboard';

$stats = [
    'products' => $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn(),
    'inquiries' => $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active = 1")->fetchColumn(),
    'views' => $pdo->query("SELECT COALESCE(SUM(views), 0) FROM products")->fetchColumn(),
];

$recentInquiries = $pdo->query("
    SELECT i.*, p.name as product_name
    FROM inquiries i
    LEFT JOIN products p ON i.product_id = p.id
    ORDER BY i.created_at DESC
    LIMIT 5
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="stats-row">
    <div class="stat-card">
        <div class="value"><?= (int)$stats['products'] ?></div>
        <div class="label">Active Products</div>
    </div>
    <div class="stat-card">
        <div class="value"><?= (int)$stats['inquiries'] ?></div>
        <div class="label">New Inquiries</div>
    </div>
    <div class="stat-card">
        <div class="value"><?= (int)$stats['categories'] ?></div>
        <div class="label">Categories</div>
    </div>
    <div class="stat-card">
        <div class="value"><?= number_format((int)$stats['views']) ?></div>
        <div class="label">Total Product Views</div>
    </div>
</div>

<div class="card">
    <h2>Recent Inquiries</h2>
    <?php if (empty($recentInquiries)): ?>
        <p style="color: var(--text-muted);">No inquiries yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentInquiries as $inq): ?>
                <tr>
                    <td><?= htmlspecialchars($inq['name']) ?></td>
                    <td><?= htmlspecialchars($inq['email']) ?></td>
                    <td><?= htmlspecialchars($inq['service'] ?: '—') ?></td>
                    <td><span class="badge badge-<?= htmlspecialchars($inq['status']) ?>"><?= htmlspecialchars(ucfirst($inq['status'])) ?></span></td>
                    <td><?= date('M j, Y', strtotime($inq['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top: 1rem;"><a href="inquiries.php" class="btn btn-primary btn-sm">View All Inquiries</a></p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
