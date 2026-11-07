<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Projects';
$pageDescription = 'The kinds of technology work GITSystem does. Individual project write-ups are not published yet.';
$navCurrent = 'projects';
$seoKey = 'projects';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="services">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Projects</span>
                <h2>The work we take on</h2>
                <p>Named project write-ups and photographs are not published yet. These are the kinds of jobs the services page already describes.</p>
            </div>
            <div class="why-grid">
                <?php foreach (content_or_default($pdo, 'project') as $item): ?>
                <article class="why-card">
                    <h3><?= frontend_h($item['title']) ?></h3>
                    <p><?= frontend_h($item['body']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
            <p style="text-align:center;margin-top:2rem;"><a href="/contact" class="btn btn-primary">Ask about a job</a></p>
        </div>
    </section>
<?php require __DIR__ . '/includes/page-end.php'; ?>
