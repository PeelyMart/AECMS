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

// Retrieve the stored user data from the session
$userData = $_SESSION['userData'];

// Return a JSON response containing the logged-in user's information
echo json_encode([
	"status" => "success", // Indicates the request was successful
	"id" => (int) $userData['id'], // User ID (converted to integer)
	"firstName" => $userData['firstName'], // User's first name
	"lastName" => $userData['lastName'], // User's last name
	"role" => $userData['role'] // User's role (e.g., ADMIN or NORMAL)
]);
?>