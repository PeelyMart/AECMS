<?php

require 'database/DBConnection.php';
$id = $_GET['id'];

$sql = $conn->prepare("SELECT * FROM products WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();

$result = $sql->get_result();
echo json_encode($result->fetch_assoc());






?>
