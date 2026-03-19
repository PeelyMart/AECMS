<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userData'])) {
	echo json_encode([
		"status" => "error",
		"errorMsg" => "not authenticated"
	]);
	exit;
}

$userData = $_SESSION['userData'];

echo json_encode([
	"status" => "success",
	"firstName" => $userData['firstName'],
	"lastName" => $userData['lastName']
]);
?>
