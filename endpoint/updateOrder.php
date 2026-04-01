<?php
require __DIR__ . '/database/DBConnection.php';
session_start();
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;

    $stmt = $conn->prepare("UPDATE orders_header SET assigned_to = ? WHERE id = ?");
    $stmt->bind_param("ii", $assigned_to, $order_id);
    $stmt->execute();
}

header("Location: a-dashboard.php");
exit;
