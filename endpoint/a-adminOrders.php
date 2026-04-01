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

try {
    $query = "
        SELECT 
            oh.id,
            oh.ext_id,
            oh.platform,
            oh.buyer_username,
            oh.total_worth,
            oh.status,
            oh.created_at,
            u.firstName,
            u.lastName,
            u.id as user_id
        FROM orders_header oh
        LEFT JOIN users u ON oh.assigned_to = u.id
        ORDER BY oh.created_at DESC
    ";

    $result = $conn->query($query);

    if (!$result) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "errorMsg" => "Database query error: " . $conn->error
        ]);
        exit;
    }

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    echo json_encode($orders);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "errorMsg" => $e->getMessage()
    ]);
}
?>
