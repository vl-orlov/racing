-- Таблица товаров
CREATE TABLE products (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name            VARCHAR(255)    NOT NULL,
    description     TEXT,
    price           DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    image_path      VARCHAR(500),
    created_at      INT UNSIGNED    NOT NULL DEFAULT UNIX_TIMESTAMP(),
    updated_at      INT UNSIGNED    NOT NULL DEFAULT UNIX_TIMESTAMP(),

    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

