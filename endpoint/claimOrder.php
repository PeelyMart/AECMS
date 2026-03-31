<?php
require 'database/DBConnection.php';
session_start();
if(!isset($_SESSION['userData'])){
	header("Location: ../index.html");
}

$user = $_SESSION['userData'];
$userID = $user['id'];
$order_headerID = $_GET['id'];



$sqlCheck = "SELECT  assigned_to FROM orders_header WHERE id =" . $order_headerID;
$result = $conn->query($sqlCheck);
$row =$result -> fetch_assoc();


if($row["assigned_to"] === null){
	$sql = "UPDATE orders_header SET assigned_to = ? WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ii", $userID, $order_headerID);
	$stmt->execute();
	header("Location: ../public/u-dashboard.html");
}
else{
	header("Location: ../public/u-dashboard.html");
}






?>
