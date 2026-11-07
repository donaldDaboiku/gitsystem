<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Services';
$pageDescription = 'Hardware, repairs, software, business automation, networking, consultancy, installation, and technology support.';
$navCurrent = 'services';
$seoKey = 'services';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="services">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Services</span>
                <h2>What GITSystem does</h2>
                <p>Ask about any of these from the contact page.</p>
            </div>
            <div class="services-grid">
                <?php foreach (content_or_default($pdo, 'service') as $item): ?>
                <div class="service-card">
                    <h3><?= frontend_h($item['title']) ?></h3>
                    <p><?= frontend_h($item['body']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <p style="text-align:center;margin-top:2rem;"><a href="/contact" class="btn btn-primary">Talk to our team</a></p>
        </div>
    </section>
<?php require __DIR__ . '/includes/page-end.php'; ?>
