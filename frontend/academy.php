<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'GIT Academy';
$pageDescription = 'Training programmes from GITSystem. Enrol by sending an enquiry.';
$navCurrent = 'academy';
$seoKey = 'academy';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="academy">
        <div class="academy-content">
            <div class="academy-info">
                <h2>GITSystem Academy</h2>
                <p>Training programmes you can ask about. Enrolment is an enquiry, not an online checkout.</p>
                <ul class="courses-list">
                    <?php foreach (content_or_default($pdo, 'course') as $item): ?>
                    <li><strong><?= frontend_h($item['title']) ?></strong> — <?= frontend_h($item['body']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="/contact" class="btn btn-primary">Enrolment enquiry</a>
            </div>
            <div class="academy-features">
                <div class="feature-box">
                    <h4>Instructors</h4>
                    <p>Learn from people who do the work.</p>
                </div>
                <div class="feature-box">
                    <h4>Practice</h4>
                    <p>Hands-on labs, not slides alone.</p>
                </div>
                <div class="feature-box">
                    <h4>Repair training</h4>
                    <p>Hardware courses for technicians.</p>
                </div>
                <div class="feature-box">
                    <h4>Ask first</h4>
                    <p>Send the course name on the contact form.</p>
                </div>
            </div>
        </div>
    </section>
<?php require __DIR__ . '/includes/page-end.php'; ?>
