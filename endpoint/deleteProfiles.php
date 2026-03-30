<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: a-profiles.php?msg=deleted");
    } else {
        header("Location: a-profiles.php?msg=error");
    }
    exit;
} else {
    header("Location: a-profiles.php");
    exit;
}