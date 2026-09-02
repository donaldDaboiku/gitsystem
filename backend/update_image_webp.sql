-- Run once on existing database
ALTER TABLE product_images
    ADD COLUMN webp_path VARCHAR(255) NULL AFTER image_path;
