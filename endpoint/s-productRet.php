<?php
session_start();
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}


require 'database/DBConnection.php';
$id = $_GET['id'];

$sql = $conn->prepare("SELECT * FROM products WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();

$result = $sql->get_result();
echo json_encode($result->fetch_assoc());






?>
