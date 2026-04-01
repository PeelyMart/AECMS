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


$result = $conn->query("SELECT id, firstName, lastName, email, status, role FROM users");

$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
