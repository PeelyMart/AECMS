
-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,   -- hashed password
    status ENUM('PENDING', 'ACTIVE', 'INACTIVE') DEFAULT 'PENDING',
    role ENUM('NORMAL', 'ADMIN') DEFAULT 'NORMAL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

