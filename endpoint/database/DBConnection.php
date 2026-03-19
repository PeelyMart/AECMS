<?php
require __DIR__ . '/../../vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create a mysqli connection and store it in a variable
$conn = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);

// Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
