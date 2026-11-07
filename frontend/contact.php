<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Contact';
$pageDescription = 'Contact GITSystem for a consultation or an enrolment enquiry.';
$navCurrent = 'contact';
$seoKey = 'contact';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="contact" id="contact">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Contact</span>
                <h2>Talk to our team</h2>
                <p>Phone, email, and address come from the site settings. WhatsApp uses the number saved there.</p>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <h2>GITSystem</h2>
                    <div class="contact-details">
                        <div class="contact-item">
                            <div>
                                <strong>Address</strong><br>
                                <span data-setting="site_address">Lagos, Nigeria</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div>
                                <strong>Phone</strong><br>
                                <a data-setting="site_phone_link" href="tel:">+234 XXX XXX XXXX</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div>
                                <strong>Email</strong><br>
                                <a data-setting="site_email_link" href="mailto:">info@gitsystemng.com</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div>
                                <strong>WhatsApp</strong><br>
                                <a data-social="whatsapp" href="#" target="_blank" rel="noopener noreferrer">Message on WhatsApp</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div>
                                <strong>Business hours</strong><br>
                                Mon–Sat: 8:00 AM – 6:00 PM
                            </div>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <form id="contactForm">
                        <input type="hidden" id="product_id" name="product_id" value="">
                        <div class="hp-field" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="service">Service Interested In *</label>
                            <select id="service" name="service" required>
                                <option value="">Select a service</option>
                                <option value="software">Software Solutions</option>
                                <option value="hardware">Hardware Services</option>
                                <option value="networking">Networking</option>
                                <option value="academy">Tech Academy</option>
                                <option value="consultation">IT Consultation</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php require __DIR__ . '/includes/page-end.php'; ?>
