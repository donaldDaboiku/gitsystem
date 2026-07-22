-- Run once in phpMyAdmin on your existing gitsystemng database.
-- If a column already exists, skip that ALTER line and continue.

ALTER TABLE products
    ADD COLUMN is_external_app TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active,
    ADD COLUMN learn_url VARCHAR(500) NULL AFTER is_external_app,
    ADD COLUMN launch_url VARCHAR(500) NULL AFTER learn_url,
    ADD COLUMN logo_url VARCHAR(500) NULL AFTER launch_url,
    ADD COLUMN brand_color VARCHAR(20) NULL DEFAULT '#109d48' AFTER logo_url,
    ADD COLUMN price_label VARCHAR(100) NULL AFTER brand_color;

-- GITInvoice as launchable external app
UPDATE products SET
    is_external_app = 1,
    learn_url = 'https://git-invoice-three.vercel.app/',
    launch_url = 'https://git-invoice-three.vercel.app/app',
    logo_url = 'https://git-invoice-three.vercel.app/icons/icon-192-maskable.png',
    brand_color = '#ff6b35',
    price_label = 'Try free · 3 invoices',
    badge = 'Live'
WHERE slug IN ('gitinvoice', 'invohub-invoicing-software');

-- ChurchMS (set Learn URL + Launch URL in Admin → Products when your app is live)
INSERT INTO products (
    category_id, name, slug, short_description, description, price, badge,
    is_featured, is_active, stock_quantity,
    is_external_app, learn_url, launch_url, logo_url, brand_color, price_label
)
SELECT 2, 'ChurchMS', 'churchms',
    'Church management system for congregations',
    'ChurchMS helps churches manage members, attendance, giving, events, and communications from one place.',
    1.00, 'Live', 1, 1, 99,
    1, '', '', '', '#109d48', 'Launch ChurchMS'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'churchms');
