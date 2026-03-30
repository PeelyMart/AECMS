<?php
require 'database/DBConnection.php';

$sql = "SELECT * FROM orders_header WHERE assigned_to is null ORDER BY created_at";
$result = $conn->query($sql);
$output =[];
while($row = $result ->fetch_assoc()){
	$output[] = $row;
}


header('Content-Type: application/json');
echo json_encode($output);




?>
