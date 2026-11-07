<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Software';
$pageDescription = 'Software products published from the GITSystem catalog, plus TailorMate.';
$navCurrent = 'software';
$seoKey = 'software';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="software-showcase">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Software</span>
                <h2>Software solutions</h2>
                <p>Only products saved in the admin catalog are listed here. TailorMate opens on its own address.</p>
            </div>
            <div class="software-grid" style="margin-bottom:1.25rem;">
                <a class="software-card" href="https://gitsystemng.com/tailormate">
                    <h3>TailorMate</h3>
                    <p>Open the TailorMate application.</p>
                    <span class="software-link">gitsystemng.com/tailormate</span>
                </a>
            </div>
            <div class="software-grid" id="softwareGrid"></div>
        </div>
    </section>
<?php require __DIR__ . '/includes/page-end.php'; ?>
