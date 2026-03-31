<?php
require 'database/DBconnection.php';
session_start();
if(!isset($_SESSION['userData'])){
	header("Location: ../index.html");
}
$user = $_SESSION['userData'];
$userID = $user['id'];
$sql = "SELECT * FROM orders_header WHERE status = 'pending' AND assigned_to = " . $userID;
$result = $conn->query($sql);
$output =[];
while($row = $result ->fetch_assoc()){
	$output[] = $row;
}


header('Content-Type: application/json');
echo json_encode($output);

?>
