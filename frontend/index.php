<?php
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('#^/frontend/index\.(php|html)(?:\?|$)#i', $uri)) {
    header('Location: /', true, 301);
    exit;
}
require_once __DIR__ . '/bootstrap.php';
$navCurrent = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require __DIR__ . '/includes/head-home.php'; ?>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="grid-overlay"></div>
        <div class="glow-orb orb-1"></div>
        <div class="glow-orb orb-2"></div>
        <div class="glow-orb orb-3"></div>
    </div>
    
    <?php require __DIR__ . '/includes/nav.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <div class="hero-text">
                <p class="hero-eyebrow">GreenPasture Integrated Tech System</p>
                <h1>Building Smarter Businesses Through Technology.</h1>
                <p class="hero-lead">GIT System supplies hardware, builds software, installs networks, and trains teams. One company for the technology a business runs on.</p>
                <div class="cta-buttons">
                    <a href="/software" class="btn btn-primary">Explore Our Solutions</a>
                    <a href="/contact" class="btn btn-secondary">Talk to Our Team</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="tech-grid">
                    <div class="tech-card">
                        <div class="tech-icon">💻</div>
                        <h3>Software</h3>
                        <p>Expert Solutions</p>
                    </div>
                    <div class="tech-card">
                        <div class="tech-icon">🔧</div>
                        <h3>Hardware</h3>
                        <p>Sales & Repair</p>
                    </div>
                    <div class="tech-card">
                        <div class="tech-icon">🌐</div>
                        <h3>Networking</h3>
                        <p>Infrastructure</p>
                    </div>
                    <div class="tech-card">
                        <div class="tech-icon">🎓</div>
                        <h3>Academy</h3>
                        <p>Tech Training</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="why" id="why">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Why GIT System</span>
                <h2>Technology support in one place</h2>
                <p>Software, hardware, networks, and training, handled by the same team.</p>
            </div>
            <div class="why-grid">
                <article class="why-card">
                    <h3>Software</h3>
                    <p>Business applications you can launch or enquire about from this site.</p>
                </article>
                <article class="why-card">
                    <h3>Hardware</h3>
                    <p>Sales, repair, and maintenance for the machines your office uses.</p>
                </article>
                <article class="why-card">
                    <h3>Networks</h3>
                    <p>Installation and upkeep for the connections between your offices.</p>
                </article>
                <article class="why-card">
                    <h3>Academy</h3>
                    <p>Training programmes your staff can enquire about from the contact form.</p>
                </article>
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-header">
                <h2><span class="gradient-text">Our Services</span></h2>
                <p>Comprehensive technology solutions tailored to your needs</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">💻</div>
                    <h3>Software Solutions</h3>
                    <p>Professional software development and integration services</p>
                    <ul>
                        <li>Custom Software Development</li>
                        <li>Application Installation & Configuration</li>
                        <li>Software Licensing & Support</li>
                        <li>System Integration</li>
                        <li>Software Updates & Maintenance</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🖥️</div>
                    <h3>Hardware Services</h3>
                    <p>Quality hardware sales, repairs, and upgrades</p>
                    <ul>
                        <li>Computer Sales & Assembly</li>
                        <li>Hardware Repairs & Diagnostics</li>
                        <li>Component Upgrades</li>
                        <li>Warranty Services</li>
                        <li>Preventive Maintenance</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🌐</div>
                    <h3>Networking Solutions</h3>
                    <p>Enterprise-grade networking infrastructure and management</p>
                    <ul>
                        <li>Network Design & Implementation</li>
                        <li>WiFi Installation & Optimization</li>
                        <li>Server Setup & Management</li>
                        <li>Network Security</li>
                        <li>Cloud Infrastructure</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🛍️</div>
                    <h3>Tech Accessories</h3>
                    <p>Wide range of quality technology accessories</p>
                    <ul>
                        <li>Keyboards, Mice & Peripherals</li>
                        <li>Cables & Adapters</li>
                        <li>Storage Devices</li>
                        <li>Monitors & Displays</li>
                        <li>Networking Equipment</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🎓</div>
                    <h3>Tech Academy</h3>
                    <p>Professional training and certification programs</p>
                    <ul>
                        <li>Programming & Development</li>
                        <li>Networking Certifications</li>
                        <li>Cybersecurity Training</li>
                        <li>Hardware Repair Courses</li>
                        <li>Digital Skills Development</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">💼</div>
                    <h3>IT Consultation</h3>
                    <p>Expert advice for your technology needs</p>
                    <ul>
                        <li>Technology Strategy</li>
                        <li>System Audits & Assessment</li>
                        <li>Digital Transformation</li>
                        <li>IT Infrastructure Planning</li>
                        <li>Vendor Selection & Management</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Software Solutions (direct landing page links) -->
    <section class="software-showcase" id="software">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Software</span>
                <h2><span class="gradient-text">Our Software Solutions</span></h2>
                <p>Launch apps open in a new tab — other solutions have detail pages on this site</p>
            </div>
            <div class="software-grid" id="softwareGrid">
                <!-- Filled from database (external apps + software products) -->
            </div>
        </div>
    </section>
    
    <!-- Products Section -->
    <section class="products" id="products">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Catalog</span>
                <h2><span class="gradient-text">Featured Products</span></h2>
                <p>Browse our selection — each product has its own page with full details, pricing, and inquiry options</p>
            </div>
            
            <!-- Category Filter -->
            <div class="product-filters">
                <button class="filter-btn active" data-category="all">All Products</button>
            </div>
            
            <!-- Products Grid (loaded dynamically via app.js) -->
            <div class="products-grid">
                <p class="products-loading">Loading products…</p>
            </div>
        </div>
    </section>
    
    <!-- Academy Section -->
    <section class="academy" id="academy">
        <div class="academy-content">
            <div class="academy-info">
                <h2><span class="gradient-text">GITSYSTEMNG Academy</span></h2>
                <p>Transform your career with industry-leading tech training programs. Our expert instructors provide hands-on learning experiences that prepare you for real-world challenges.</p>
                
                <ul class="courses-list">
                    <li><strong>Web Development</strong> - Full-stack development with modern frameworks</li>
                    <li><strong>Network Engineering</strong> - CCNA, CCNP & enterprise networking</li>
                    <li><strong>Cybersecurity</strong> - Ethical hacking & security analysis</li>
                    <li><strong>Hardware Repair</strong> - Professional computer technician training</li>
                    <li><strong>Data Science</strong> - Analytics, ML & AI fundamentals</li>
                    <li><strong>Cloud Computing</strong> - AWS, Azure & Google Cloud</li>
                </ul>
                
                <a href="/academy" class="btn btn-primary">GIT Academy</a>
            </div>
            
            <div class="academy-features">
                <div class="feature-box">
                    <h4>👨‍🏫 Expert Instructors</h4>
                    <p>Learn from industry professionals</p>
                </div>
                <div class="feature-box">
                    <h4>🏆 Certifications</h4>
                    <p>Industry-recognized credentials</p>
                </div>
                <div class="feature-box">
                    <h4>💻 Hands-On Labs</h4>
                    <p>Practical, real-world training</p>
                </div>
                <div class="feature-box">
                    <h4>🤝 Job Placement</h4>
                    <p>Career support & guidance</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <div class="section-header">
                <h2><span class="gradient-text">Get In Touch</span></h2>
                <p>Ready to transform your technology experience? Contact us today</p>
            </div>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h2>Let's Work Together</h2>
                    <p>Whether you need repairs, training, or consultation, we're here to help. Reach out and let's discuss how we can serve you better.</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <span class="contact-icon">📍</span>
                            <div>
                                <strong>Address</strong><br>
                                <span data-setting="site_address">Lagos, Nigeria</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">📞</span>
                            <div>
                                <strong>Phone</strong><br>
                                <a data-setting="site_phone_link" href="tel:">+234 XXX XXX XXXX</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">📧</span>
                            <div>
                                <strong>Email</strong><br>
                                <a data-setting="site_email_link" href="mailto:">info@gitsystemng.com</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">⏰</span>
                            <div>
                                <strong>Business Hours</strong><br>
                                Mon - Sat: 8:00 AM - 6:00 PM
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" data-social="facebook" title="Facebook" target="_blank" rel="noopener noreferrer">📘</a>
                        <a href="#" data-social="twitter" title="Twitter" target="_blank" rel="noopener noreferrer">🐦</a>
                        <a href="#" data-social="instagram" title="Instagram" target="_blank" rel="noopener noreferrer">📷</a>
                        <a href="#" data-social="linkedin" title="LinkedIn" target="_blank" rel="noopener noreferrer">💼</a>
                        <a href="#" data-social="whatsapp" title="WhatsApp" target="_blank" rel="noopener noreferrer">💬</a>
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
                                <option value="accessories">Tech Accessories</option>
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
    
    <section class="contact-cta">
        <div class="container">
            <h2>Talk to our team</h2>
            <p>Send a consultation request. We reply using the phone and email saved in site settings.</p>
            <a href="/contact" class="btn btn-primary">Contact GIT System</a>
        </div>
    </section>

    <?php require __DIR__ . '/includes/footer.php'; ?>
    
    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">↑</button>
    
    <?= asset_tag_js('shared.js') ?>
    <?= asset_tag_js('app.js') ?>
</body>
</html>
