<?php
require __DIR__ . '/database/DBConnection.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data safely
    $firstName     = trim($_POST['firstName'] ?? '');
    $lastName      = trim($_POST['lastName'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $contactNumber = trim($_POST['contactNumber'] ?? '');
    $password      = trim($_POST['password'] ?? '');
    $confirm       = trim($_POST['confirm'] ?? '');

    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        die("Required fields are missing.");
    }

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO USERS (firstName, lastName, email, contactNumber, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $contactNumber, $hashedPassword);

    if ($stmt->execute()) {
        // Success → redirect to login page
        header("Location: ../index.html");
        exit;
    } else {
        die("Error inserting record: " . $stmt->error);
    }
} else {
    // If accessed directly, redirect to register page
    header("Location: register.html");
    exit;
}
