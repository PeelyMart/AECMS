<?php
require 'database/DBConnection.php';

$id = $_GET['id'];

$sql = $conn->prepare("DELETE from PRODUCTS where id = ? ");
$sql->bind_param("i", $id);
$sql->execute();


header("Location: ../public/a-readProd.html");


?>
