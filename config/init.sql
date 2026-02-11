-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL ,
    contactNumber VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,   -- hashed password
    status ENUM('PENDING', 'ACTIVE', 'INACTIVE') DEFAULT 'PENDING',
    role ENUM('NORMAL', 'ADMIN') DEFAULT 'NORMAL',
);

