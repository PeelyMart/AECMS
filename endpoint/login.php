<?php
session_start();

$username = $_POST['username'] ?? '';
$password= $_POST['password'] ?? '';



//for testing purposes only
if($username === "admin" && $password === "1234"){
	$_SESSION['user'] = $username;
	echo json_encode([
		"status" => "success" 
	]);
} else{
	echo json_encode([ 
		"status" => "error",
		"errorMsg" => "invalid password"
	]);

}


?>




