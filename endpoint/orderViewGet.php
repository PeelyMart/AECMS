<?php
require __DIR__ . '/database/DBConnection.php'; 
session_start(); 
header('Content-Type: application/json'); 


if (!isset($_SESSION['userData']['id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;


if ($id <= 0) {
    http_response_code(400); // Return HTTP 400 (Bad Request) if ID is invalid
    echo json_encode([]); // Return empty JSON response
    exit; // Stop execution
}

// Prepare SQL query to retrieve order header information (order ID and assigned user)
$orderHeader = $conn->prepare("SELECT id, assigned_to FROM orders_header WHERE id = ? LIMIT 1");

// Bind the order ID parameter to the prepared statement
$orderHeader->bind_param("i", $id);

// Execute the query
$orderHeader->execute();

// Get the result of the executed query
$orderResult = $orderHeader->get_result();

// Fetch the order data as an associative array
$order = $orderResult->fetch_assoc();

// Check if the order exists
if (!$order) {
    http_response_code(404); // Return HTTP 404 (Not Found) if order does not exist
    echo json_encode([]); // Return empty JSON response
    exit; // Stop execution
}

// Additional authorization check if session user data exists
if (isset($_SESSION['userData'])) {

    // Get the logged-in user's ID
    $userId = (int) ($_SESSION['userData']['id'] ?? 0);

    // Get the user's role and convert to uppercase (ADMIN or NORMAL)
    $role = strtoupper($_SESSION['userData']['role'] ?? 'NORMAL');

    // If the user is not ADMIN and the order is assigned to someone else, block access
    if ($role !== 'ADMIN' && $order['assigned_to'] !== null && (int) $order['assigned_to'] !== $userId) {
        http_response_code(403); // Return HTTP 403 (Forbidden)
        echo json_encode([]); // Return empty JSON response
        exit; // Stop execution
    }
}

// SQL query to retrieve the order items belonging to the specified order
$sql = "
    SELECT 
        COALESCE(p.id, oi.product_id) AS id,
        COALESCE(p.name, oi.external_sku) AS name,
        oi.external_sku,
        oi.qty,
        oi.unit_price_snapshot,
        oi.sub_total
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
";

// Prepare the SQL statement for execution
$stmt = $conn->prepare($sql);

// Bind the order ID parameter to the query
$stmt->bind_param("i", $id);

// Execute the query
$stmt->execute();

// Retrieve the result set
$result = $stmt->get_result();

// Initialize an empty array to store the order items
$data = [];

// Loop through each returned row and add it to the data array
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Convert the array of order items to JSON format and return it as the response
echo json_encode($data);
?>