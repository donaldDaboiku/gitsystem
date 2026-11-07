-- Phase 5 tables for an existing database. Do not import database.sql.
-- Opening the website also creates these tables and fills the current page text.
-- Testimonials are left empty on purpose.

CREATE TABLE IF NOT EXISTS content_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kind ENUM('service','project','course','testimonial') NOT NULL,
    title VARCHAR(180) NOT NULL,
    body TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_kind_sort (kind, sort_order, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS page_seo (
    page_key VARCHAR(40) PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description VARCHAR(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
