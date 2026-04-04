<?php
session_start();
require __DIR__ . '/database/DBConnection.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}

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
    $query = "SELECT id, firstName, lastName FROM users WHERE role = 'NORMAL' AND status = 'ACTIVE' ORDER BY firstName, lastName";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }

    $staff = [];
    while ($row = $result->fetch_assoc()) {
        $staff[] = $row;
    }

    echo json_encode($staff);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "errorMsg" => $e->getMessage()
    ]);
}
?>
