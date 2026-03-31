<?php
require __DIR__ . '/database/DBConnection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userData']['id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT id, ext_id, platform, buyer_username, total_worth, status, created_at
    FROM orders_header
    WHERE assigned_to IS NULL
    ORDER BY created_at DESC
";
$result = $conn->query($sql);
$output = [];

while ($row = $result->fetch_assoc()) {
    $output[] = $row;
}

echo json_encode($output);
?>
