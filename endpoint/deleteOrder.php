<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM orders_header WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: a-dashboard.php");
exit;