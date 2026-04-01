<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

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

$analytics = [];

// 1. User with most orders packed this month
$query1 = "
    SELECT 
        u.id,
        u.firstName,
        u.lastName,
        COUNT(oh.id) as packed_count
    FROM users u
    JOIN orders_header oh ON u.id = oh.assigned_to
    WHERE oh.status = 'packed' 
        AND MONTH(oh.created_at) = MONTH(NOW()) 
        AND YEAR(oh.created_at) = YEAR(NOW())
    GROUP BY u.id, u.firstName, u.lastName
    ORDER BY packed_count DESC
    LIMIT 1
";

$result1 = $conn->query($query1);
if (!$result1) {
    die("Query error: " . $conn->error);
}
$topPacker = $result1->fetch_assoc();
$analytics['topPacker'] = $topPacker ?? [
    'firstName' => 'N/A',
    'lastName' => '',
    'packed_count' => 0
];

// 2. Products with most order items this month
$query2 = "
    SELECT 
        p.id,
        p.name,
        SUM(oi.qty) as total_qty,
        COUNT(DISTINCT oi.order_id) as order_count
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    JOIN orders_header oh ON oi.order_id = oh.id
    WHERE MONTH(oh.created_at) = MONTH(NOW()) 
        AND YEAR(oh.created_at) = YEAR(NOW())
    GROUP BY p.id, p.name
    ORDER BY total_qty DESC
    LIMIT 1
";

$result2 = $conn->query($query2);
if (!$result2) {
    die("Query error: " . $conn->error);
}
$topProduct = $result2->fetch_assoc();
$analytics['topProduct'] = $topProduct ?? [
    'name' => 'N/A',
    'total_qty' => 0,
    'order_count' => 0
];

// 3. Total worth of packages this month
$query3 = "
    SELECT 
        SUM(total_worth) as total_worth,
        COUNT(id) as order_count
    FROM orders_header
    WHERE MONTH(created_at) = MONTH(NOW()) 
        AND YEAR(created_at) = YEAR(NOW())
";

$result3 = $conn->query($query3);
if (!$result3) {
    die("Query error: " . $conn->error);
}
$monthTotal = $result3->fetch_assoc();
$analytics['monthTotal'] = $monthTotal ?? [
    'total_worth' => 0,
    'order_count' => 0
];

// 4. Last 6 months worth of packages
$query4 = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(total_worth) as total_worth,
        COUNT(id) as order_count
    FROM orders_header
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
";

$result4 = $conn->query($query4);
if (!$result4) {
    die("Query error: " . $conn->error);
}
$sixMonthData = [];
while ($row = $result4->fetch_assoc()) {
    $sixMonthData[] = $row;
}
$analytics['sixMonthData'] = $sixMonthData ?? [];

echo json_encode($analytics);
?>
