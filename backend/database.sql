-- GITSYSTEMNG Database Schema
-- Run this SQL in your MySQL database via phpMyAdmin or command line

CREATE DATABASE IF NOT EXISTS gitsystemng CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gitsystemng;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (username: admin, password: admin123 - CHANGE THIS!)
INSERT INTO admin_users (username, password, email, full_name) 
VALUES ('admin', '$2y$12$ccJpRl4Wur8gHQZpzdV9U.ngz6i.8aeqDf8zDlwptwIFVgEWvHpZG', 'admin@gitsystemng.com', 'Administrator');

-- Product categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default categories
INSERT INTO categories (name, slug, description, display_order) VALUES
('Laptops', 'laptops', 'Business and personal laptops', 1),
('Software', 'software', 'Software and applications', 2),
('Accessories', 'accessories', 'Computer accessories and peripherals', 3),
('Networking', 'networking', 'Networking equipment and solutions', 4),
('Components', 'components', 'Computer components and parts', 5),
('Peripherals', 'peripherals', 'Monitors, webcams, and other peripherals', 6);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10, 2) NOT NULL,
    original_price DECIMAL(10, 2),
    stock_quantity INT DEFAULT 0,
    sku VARCHAR(50) UNIQUE,
    badge VARCHAR(50),
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_external_app BOOLEAN DEFAULT FALSE,
    learn_url VARCHAR(500) NULL,
    launch_url VARCHAR(500) NULL,
    logo_url VARCHAR(500) NULL,
    brand_color VARCHAR(20) DEFAULT '#109d48',
    price_label VARCHAR(100) NULL,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product images table
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    alt_text VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product specifications table
CREATE TABLE IF NOT EXISTS product_specs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    spec_name VARCHAR(100) NOT NULL,
    spec_value TEXT NOT NULL,
    display_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inquiries/Contact form submissions
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service VARCHAR(100),
    product_id INT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'contacted', 'resolved', 'spam') DEFAULT 'new',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'GITSYSTEM', 'text', 'Website name'),
('site_email', 'info@gitsystemng.com', 'email', 'Contact email'),
('site_phone', '+234 XXX XXX XXXX', 'text', 'Contact phone'),
('site_address', 'Lagos, Nigeria', 'text', 'Business address'),
('currency_symbol', '₦', 'text', 'Currency symbol'),
('facebook_url', '#', 'url', 'Facebook page URL'),
('twitter_url', '#', 'url', 'Twitter profile URL'),
('instagram_url', '#', 'url', 'Instagram profile URL'),
('linkedin_url', '#', 'url', 'LinkedIn profile URL'),
('whatsapp_number', '', 'text', 'WhatsApp business number');

-- Activity log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample products (matches frontend catalog)
INSERT INTO products (category_id, name, slug, short_description, description, price, badge, is_featured, stock_quantity, is_external_app, learn_url, launch_url, logo_url, brand_color, price_label) VALUES
(2, 'GITInvoice', 'gitinvoice', 'Online invoicing & payment tracking for businesses', 'GITInvoice is our cloud invoicing platform. Create professional invoices, track payments, and manage clients online.', 35000.00, 'Live', 1, 99, 1, 'https://git-invoice-three.vercel.app/', 'https://git-invoice-three.vercel.app/app', 'https://git-invoice-three.vercel.app/icons/icon-192-maskable.png', '#ff6b35', 'Try free · 3 invoices'),
(2, 'ChurchMS', 'churchms', 'Church management system for congregations', 'ChurchMS helps churches manage members, attendance, giving, events, and communications from one place.', 1.00, 'Live', 1, 99, 1, '', '', '', '#109d48', 'Launch ChurchMS'),
(2, 'SMS - School Management System', 'sms-school-management-system', 'School management software for schools', 'End-to-end school administration: admissions, fees, attendance, and grading.', 850000.00, 'New', 1, 10, 0, NULL, NULL, NULL, NULL, NULL),
(2, 'HMS - Hospital Management System', 'hms-hospital-management-system', 'Hospital management software for hospitals', 'Patient records, appointments, billing, and pharmacy management.', 1200000.00, 'New', 1, 5, 0, NULL, NULL, NULL, NULL, NULL),
(2, 'FSM - Fashion Management System', 'fsm-fashion-management-system', 'Fashion management software for fashion businesses', 'Inventory, orders, and customer management for fashion retailers.', 500000.00, 'New', 0, 15, 0, NULL, NULL, NULL, NULL, NULL),
(1, 'Dell Latitude 5420', 'dell-latitude-5420', 'Intel Core i5, 16GB RAM, 512GB SSD', 'Business laptop with Intel Core i5, 16GB RAM, 512GB SSD.', 450000.00, 'New', 1, 8, 0, NULL, NULL, NULL, NULL, NULL),
(1, 'HP ProBook 450 G8', 'hp-probook-450-g8', 'Intel Core i7, 16GB RAM, 1TB SSD', 'Reliable business laptop with Intel Core i7, 16GB RAM, 1TB SSD.', 580000.00, NULL, 0, 6, 0, NULL, NULL, NULL, NULL, NULL),
(1, 'Lenovo ThinkPad T14', 'lenovo-thinkpad-t14', 'AMD Ryzen 7, 32GB RAM, 512GB SSD', 'Professional laptop with AMD Ryzen 7, 32GB RAM, 512GB SSD.', 520000.00, 'Sale', 1, 4, 0, NULL, NULL, NULL, NULL, NULL),
(3, 'Logitech MX Master 3S', 'logitech-mx-master-3s', 'Ergonomic, 8K DPI, Multi-device', 'Premium wireless mouse with ergonomic design and multi-device support.', 45000.00, NULL, 0, 25, 0, NULL, NULL, NULL, NULL, NULL),
(3, 'Keychron K8 Pro', 'keychron-k8-pro', 'Hot-swappable, RGB, Wireless', 'Mechanical keyboard with hot-swappable switches and wireless connectivity.', 65000.00, 'Popular', 1, 20, 0, NULL, NULL, NULL, NULL, NULL),
(3, 'Sony WH-1000XM5', 'sony-wh-1000xm5', 'Premium audio, 30hr battery', 'Industry-leading noise cancelling headphones with 30-hour battery.', 180000.00, NULL, 0, 12, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'UniFi Dream Machine', 'unifi-dream-machine', 'Enterprise router with WiFi 6', 'All-in-one network security gateway with WiFi 6.', 250000.00, NULL, 0, 7, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'TP-Link Omada EAP660 HD', 'tp-link-omada-eap660-hd', '3.6 Gbps, PoE, Ceiling Mount', 'WiFi 6 access point with 3.6 Gbps throughput and PoE support.', 95000.00, 'New', 0, 15, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'Cisco SG350-28P', 'cisco-sg350-28p', '28-port Gigabit with 24 PoE+', 'Managed PoE switch with 28 Gigabit ports.', 380000.00, NULL, 0, 5, 0, NULL, NULL, NULL, NULL, NULL),
(5, 'NVIDIA RTX 4060 Ti', 'nvidia-rtx-4060-ti', '8GB GDDR6, Ray Tracing', 'High-performance graphics card with 8GB GDDR6 and ray tracing.', 320000.00, 'Sale', 1, 9, 0, NULL, NULL, NULL, NULL, NULL),
(5, 'Intel Core i7-13700K', 'intel-core-i7-13700k', '16-core, 24-thread, 5.4GHz', 'Desktop processor with 16 cores and up to 5.4GHz boost.', 280000.00, NULL, 0, 11, 0, NULL, NULL, NULL, NULL, NULL),
(5, 'Samsung 990 PRO', 'samsung-990-pro', '2TB, 7450MB/s read speed', 'NVMe SSD with 2TB capacity and 7450MB/s read speed.', 145000.00, 'Popular', 1, 18, 0, NULL, NULL, NULL, NULL, NULL),
(6, 'LG UltraGear 27" 4K', 'lg-ultragear-27-4k', '144Hz, 1ms, HDR400', '27-inch 4K gaming monitor with 144Hz refresh rate.', 380000.00, NULL, 0, 6, 0, NULL, NULL, NULL, NULL, NULL),
(6, 'Shure MV7', 'shure-mv7', 'Podcast & streaming quality', 'Professional USB/XLR microphone for podcasting and streaming.', 125000.00, 'New', 0, 10, 0, NULL, NULL, NULL, NULL, NULL),
(6, 'Logitech C920 Pro HD', 'logitech-c920-pro-hd', '1080p, auto-focus, stereo audio', 'Full HD webcam with auto-focus and stereo audio.', 42000.00, NULL, 0, 30, 0, NULL, NULL, NULL, NULL, NULL);

-- Product specifications for software solutions
INSERT INTO product_specs (product_id, spec_name, spec_value, display_order)
SELECT id, 'Invoicing', 'Unlimited invoices & receipts', 1 FROM products WHERE slug = 'gitinvoice'
UNION ALL SELECT id, 'Payments', 'Track paid & pending transactions', 2 FROM products WHERE slug = 'gitinvoice'
UNION ALL SELECT id, 'Reports', 'PDF & Excel export', 3 FROM products WHERE slug = 'gitinvoice'
UNION ALL SELECT id, 'Access', 'Launch instantly in your browser', 4 FROM products WHERE slug = 'gitinvoice';

INSERT INTO product_specs (product_id, spec_name, spec_value, display_order)
SELECT id, 'Members', 'Member records & groups', 1 FROM products WHERE slug = 'churchms'
UNION ALL SELECT id, 'Giving', 'Tithes & offering tracking', 2 FROM products WHERE slug = 'churchms'
UNION ALL SELECT id, 'Events', 'Services & events calendar', 3 FROM products WHERE slug = 'churchms'
UNION ALL SELECT id, 'Access', 'Launch in your browser', 4 FROM products WHERE slug = 'churchms';

INSERT INTO product_specs (product_id, spec_name, spec_value, display_order)
SELECT id, 'Modules', 'Admissions, fees, attendance, grading', 1 FROM products WHERE slug = 'sms-school-management-system'
UNION ALL SELECT id, 'Users', 'Unlimited staff accounts', 2 FROM products WHERE slug = 'sms-school-management-system'
UNION ALL SELECT id, 'Portal', 'Parent & student portals', 3 FROM products WHERE slug = 'sms-school-management-system'
UNION ALL SELECT id, 'Deployment', 'Cloud or on-premise', 4 FROM products WHERE slug = 'sms-school-management-system';

INSERT INTO product_specs (product_id, spec_name, spec_value, display_order)
SELECT id, 'Modules', 'Patients, appointments, billing, pharmacy', 1 FROM products WHERE slug = 'hms-hospital-management-system'
UNION ALL SELECT id, 'Compliance', 'NHIS-ready workflows', 2 FROM products WHERE slug = 'hms-hospital-management-system'
UNION ALL SELECT id, 'Security', 'Role-based access control', 3 FROM products WHERE slug = 'hms-hospital-management-system'
UNION ALL SELECT id, 'Training', 'Staff onboarding included', 4 FROM products WHERE slug = 'hms-hospital-management-system';

INSERT INTO product_specs (product_id, spec_name, spec_value, display_order)
SELECT id, 'Inventory', 'Stock & SKU management', 1 FROM products WHERE slug = 'fsm-fashion-management-system'
UNION ALL SELECT id, 'Orders', 'POS & online order tracking', 2 FROM products WHERE slug = 'fsm-fashion-management-system'
UNION ALL SELECT id, 'Customers', 'CRM & loyalty features', 3 FROM products WHERE slug = 'fsm-fashion-management-system'
UNION ALL SELECT id, 'Reports', 'Sales & inventory analytics', 4 FROM products WHERE slug = 'fsm-fashion-management-system';
