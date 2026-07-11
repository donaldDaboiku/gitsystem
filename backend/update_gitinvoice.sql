-- Run in phpMyAdmin if you already imported the database (replaces invoHub with GITInvoice)
UPDATE products
SET name = 'GITInvoice',
    slug = 'gitinvoice',
    short_description = 'Online invoicing & payment tracking for businesses',
    description = 'GITInvoice is our cloud invoicing platform. Create professional invoices, track payments, and manage clients online.',
    badge = 'Live'
WHERE slug IN ('invohub-invoicing-software', 'gitinvoice');
