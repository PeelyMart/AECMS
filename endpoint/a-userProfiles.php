<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

// code to ensure that admins are the only ones who can access this

/* 
if ($_SESSION['userData']['role'] !== 'admin') { 
        exit; 
} 
*/

$result = $conn->query("SELECT id, firstName, lastName, email, status, role FROM users");

$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);