<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'GIT Academy';
$pageDescription = 'Training programmes from GIT System. Enrol by sending an enquiry.';
$navCurrent = 'academy';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="academy">
        <div class="academy-content">
            <div class="academy-info">
                <h2>GIT Academy</h2>
                <p>Training programmes you can ask about. Enrolment is an enquiry, not an online checkout.</p>
                <ul class="courses-list">
                    <li><strong>Web Development</strong> — Full-stack development with modern frameworks</li>
                    <li><strong>Network Engineering</strong> — CCNA, CCNP and enterprise networking</li>
                    <li><strong>Cybersecurity</strong> — Ethical hacking and security analysis</li>
                    <li><strong>Hardware Repair</strong> — Computer technician training</li>
                    <li><strong>Data Science</strong> — Analytics, machine learning, and AI fundamentals</li>
                    <li><strong>Cloud Computing</strong> — AWS, Azure, and Google Cloud</li>
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
