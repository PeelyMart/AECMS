CREATE DATABASE IF NOT EXISTS ITPROG;
USE ITPROG;

-- =========================
-- USERS
-- =========================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    contactNumber VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('PENDING', 'ACTIVE', 'INACTIVE') DEFAULT 'PENDING',
    role ENUM('NORMAL', 'ADMIN') DEFAULT 'NORMAL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- PRODUCTS
-- =========================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,

    l_sku VARCHAR(100),
    s_sku VARCHAR(100),
    t_sku VARCHAR(100),

    qty INT DEFAULT 0,

    remarks TEXT NULL,

    unit_price DECIMAL(10,2) DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Prevent duplicate SKUs per platform
CREATE UNIQUE INDEX uniq_l_sku ON products(l_sku);
CREATE UNIQUE INDEX uniq_s_sku ON products(s_sku);
CREATE UNIQUE INDEX uniq_t_sku ON products(t_sku);

-- =========================
-- ORDERS HEADER
-- =========================
CREATE TABLE IF NOT EXISTS orders_header (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ext_id VARCHAR(100) NOT NULL,
    platform ENUM('lazada', 'shopee', 'tiktok') NOT NULL,

    buyer_username VARCHAR(150),

    total_worth DECIMAL(10,2) DEFAULT 0,

    assigned_to INT NULL,

    status ENUM('pending', 'packed', 'shipped') DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_order (ext_id, platform),

    FOREIGN KEY (assigned_to) REFERENCES users(id)
        ON DELETE SET NULL
);

-- =========================
-- ORDER ITEMS
-- =========================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    order_id INT NOT NULL,
    product_id INT NULL,

    external_sku VARCHAR(100) NOT NULL,
    qty INT NOT NULL,

    unit_price_snapshot DECIMAL(10,2) NULL,

    sub_total DECIMAL(10,2) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders_header(id)
        ON DELETE CASCADE,

    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE SET NULL
);

-- =========================
-- INDEXES (PERFORMANCE)
-- =========================
CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_order_items_product_id ON order_items(product_id);
CREATE INDEX idx_orders_assigned_to ON orders_header(assigned_to);
