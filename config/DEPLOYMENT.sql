-- =====================================
-- DATABASE SETUP
-- =====================================
CREATE DATABASE IF NOT EXISTS ITPROG;
USE ITPROG;


SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders_header;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(50) NOT NULL UNIQUE,
  firstName VARCHAR(50) NOT NULL,
  lastName VARCHAR(50) NOT NULL,
  contactNumber VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  status ENUM('PENDING','ACTIVE','INACTIVE') DEFAULT 'PENDING',
  role ENUM('NORMAL','ADMIN') DEFAULT 'NORMAL',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  l_sku VARCHAR(100) UNIQUE,
  s_sku VARCHAR(100) UNIQUE,
  t_sku VARCHAR(100) UNIQUE,
  qty INT DEFAULT 0,
  remarks TEXT,
  unit_price DECIMAL(10,2) DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders_header (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ext_id VARCHAR(100) NOT NULL,
  platform ENUM('lazada','shopee','tiktok') NOT NULL,
  buyer_username VARCHAR(150),
  total_worth DECIMAL(10,2) DEFAULT 0.00,
  assigned_to INT,
  status ENUM('pending','packed','shipped') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY unique_order (ext_id, platform),
  INDEX idx_orders_assigned_to (assigned_to),

  CONSTRAINT fk_orders_user
    FOREIGN KEY (assigned_to)
    REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT,
  external_sku VARCHAR(100) NOT NULL,
  qty INT NOT NULL,
  unit_price_snapshot DECIMAL(10,2),
  sub_total DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  INDEX idx_order_items_order_id (order_id),
  INDEX idx_order_items_product_id (product_id),

  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id)
    REFERENCES orders_header(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_order_items_product
    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;


