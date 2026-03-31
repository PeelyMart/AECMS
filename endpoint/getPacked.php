<?php
require __DIR__ . '/database/DBConnection.php'; 
session_start(); 
header('Content-Type: application/json'); 


if (!isset($_SESSION['userData']['id'])) {
    http_response_code(401); 
    echo json_encode([]); 
    exit; 
}

// Retrieve the logged-in user's ID from the session and convert it to an integer
$userId = (int) $_SESSION['userData']['id'];

// Define the order status we want to filter (only packed orders)
$status = 'packed';

// Prepare an SQL query to retrieve orders assigned to the current user with status "packed"
$stmt = $conn->prepare("
    SELECT id, ext_id, platform, buyer_username, total_worth, status, created_at
    FROM orders_header
    WHERE assigned_to = ? AND status = ?
    ORDER BY created_at DESC
");

// Bind the parameters to the prepared statement
// "is" means the first parameter is an integer (userId) and the second is a string (status)
$stmt->bind_param("is", $userId, $status);

// Execute the SQL query
$stmt->execute();

// Retrieve the result set from the executed query
$result = $stmt->get_result();

// Initialize an empty array to store the retrieved orders
$orders = [];

// Loop through each row returned by the query
while ($row = $result->fetch_assoc()) {
    $orders[] = $row; // Add each order row into the orders array
}

// Convert the orders array to JSON format and send it as the response
echo json_encode($orders);
?>