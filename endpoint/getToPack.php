<?php
require __DIR__ . '/database/DBConnection.php'; 
session_start();
header('Content-Type: application/json'); 


if (!isset($_SESSION['userData']['id'])) {
    http_response_code(401); 
    echo json_encode([]); 
    exit; 
}


$userId = (int) $_SESSION['userData']['id'];

// Define the order status filter (only retrieve orders with status "pending")
$status = 'pending';

// Prepare the SQL query to retrieve orders assigned to the current user with the specified status
$stmt = $conn->prepare("
    SELECT id, ext_id, platform, buyer_username, total_worth, status, created_at
    FROM orders_header
    WHERE assigned_to = ? AND status = ?
    ORDER BY created_at DESC
");

// Bind the parameters to the prepared statement
// "is" means the first parameter is an integer (userId) and the second parameter is a string (status)
$stmt->bind_param("is", $userId, $status);

// Execute the prepared SQL statement
$stmt->execute();

// Retrieve the result set from the executed query
$result = $stmt->get_result();

// Initialize an empty array to store the orders retrieved from the database
$orders = [];

// Loop through each row returned by the query
while ($row = $result->fetch_assoc()) {
    $orders[] = $row; // Add each order record into the orders array
}

// Convert the orders array into JSON format and return it as the response
echo json_encode($orders);
?>