ALTER TABLE products
    ADD COLUMN category VARCHAR(50) NOT NULL DEFAULT ''
    AFTER price;
