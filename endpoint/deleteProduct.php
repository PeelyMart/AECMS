<?php
require 'database/DBConnection.php';
session_start();
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}



$id = $_GET['id'];

$sql = $conn->prepare("DELETE from PRODUCTS where id = ? ");
$sql->bind_param("i", $id);
$sql->execute();


header("Location: ../public/a-readProd.html");


?>
