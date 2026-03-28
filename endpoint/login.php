<?php

if(!isset($_POST['userID'])){
	header("Location: ../index.html");

}
require __DIR__ . '/database/DBConnection.php';
session_start();

$userID = isset($_POST['userID']) ? (int) $_POST['userID'] : 0;

if(empty($_POST['password'])){
	echo json_encode([
		"status" => "error",
		"errorMsg" => "password required"
	]);
	exit;
}

$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows === 0){
	echo json_encode([
		"status" => "error",
		"errorMsg" => "userId not found"
	]);
	exit;
}

$matched = $result->fetch_assoc();
$matchedPassword = $matched['password'];

if(password_verify($password, $matchedPassword)){ 
	$_SESSION['userData'] = $matched;
	echo json_encode([
		"status" => "success"
	]);
} else {
	echo json_encode([ 
		"status" => "error",
		"errorMsg" => "invalid password"
	]);
}
?>




