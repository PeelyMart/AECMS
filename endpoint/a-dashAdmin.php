<?php
session_start();
require __DIR__ . '/database/DBConnection.php';

// Check if user is ADMIN
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}
header('Content-Type: application/json');
$userList = $conn->query("SELECT id, firstName, lastName FROM users");
$users = [];
while($u = $userList->fetch_assoc()) {
    $users[] = $u;
}

$query = "SELECT oh.*, u.firstName, u.lastName 
          FROM orders_header oh 
          LEFT JOIN users u ON oh.assigned_to = u.id 
          ORDER BY oh.created_at DESC";
$ordersResult = $conn->query($query);

$orders = [];
while($row = $ordersResult->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode([
    "users" => $users,
    "orders" => $orders
]);
