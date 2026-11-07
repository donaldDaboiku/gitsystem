<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Services';
$pageDescription = 'Hardware, repairs, software, business automation, networking, consultancy, installation, and technology support.';
$navCurrent = 'services';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="services">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Services</span>
                <h2>What GIT System does</h2>
                <p>Ask about any of these from the contact page.</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <h3>Hardware sales and support</h3>
                    <p>Computer sales, assembly, and the accessories an office needs.</p>
                </div>
                <div class="service-card">
                    <h3>Repairs and maintenance</h3>
                    <p>Diagnostics, repairs, upgrades, and preventive maintenance.</p>
                </div>
                <div class="service-card">
                    <h3>Software development</h3>
                    <p>Custom software, installation, licensing, and updates.</p>
                </div>
                <div class="service-card">
                    <h3>Business automation</h3>
                    <p>Systems that take a repeated office task and run it in software.</p>
                </div>
                <div class="service-card">
                    <h3>Network installation</h3>
                    <p>Network design, Wi-Fi, servers, and ongoing maintenance.</p>
                </div>
                <div class="service-card">
                    <h3>IT consultancy</h3>
                    <p>Technology planning, system reviews, and infrastructure advice.</p>
                </div>
                <div class="service-card">
                    <h3>Equipment installation</h3>
                    <p>Setup of the hardware and network equipment you buy.</p>
                </div>
                <div class="service-card">
                    <h3>Technology support</h3>
                    <p>Help after the installation, for the systems we put in place.</p>
                </div>
            </div>
            <p style="text-align:center;margin-top:2rem;"><a href="/contact" class="btn btn-primary">Talk to our team</a></p>
        </div>
    </section>
<?php require __DIR__ . '/includes/page-end.php'; ?>
