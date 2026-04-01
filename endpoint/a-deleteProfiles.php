<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: ../public/a-profiles.html");
exit;
