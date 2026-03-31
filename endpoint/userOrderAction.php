<?php
require __DIR__ . '/database/DBConnection.php';
session_start(); 
header('Content-Type: application/json'); 

// Check if the HTTP request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo json_encode([
        'status' => 'error', // Response status
        'errorMsg' => 'Method not allowed' // Error message returned to the client
    ]);
    exit; 
}

// Check if the user is authenticated by verifying the session user ID
if (!isset($_SESSION['userData']['id'])) {
    http_response_code(401); // Send HTTP status code 401 (Unauthorized)
    echo json_encode([
        'status' => 'error', // Response status
        'errorMsg' => 'Not authenticated' // Error message if user is not logged in
    ]);
    exit; // Stop execution
}

// Get the logged-in user's ID from the session and convert it to an integer
$userId = (int) $_SESSION['userData']['id'];

// Get the user's role from the session, defaulting to NORMAL if not set, and convert to uppercase
$role = strtoupper($_SESSION['userData']['role'] ?? 'NORMAL');

// Retrieve the order ID from POST request, convert to integer, default to 0 if not provided
$orderId = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;

// Retrieve the requested action from POST data
$action = $_POST['action'] ?? '';

// List of allowed actions that the user can perform
$allowedActions = ['claim_pack', 'pack', 'unpack'];

// Validate the order ID and action input
if ($orderId <= 0 || !in_array($action, $allowedActions, true)) {
    http_response_code(400); // Send HTTP status code 400 (Bad Request)
    echo json_encode([
        'status' => 'error', // Response status
        'errorMsg' => 'Invalid order action request' // Error message for invalid input
    ]);
    exit; // Stop execution
}

// Prepare SQL query to retrieve order details from orders_header table
$stmt = $conn->prepare("SELECT id, assigned_to, status FROM orders_header WHERE id = ? LIMIT 1");

// Bind the order ID parameter to the SQL query (i = integer)
$stmt->bind_param("i", $orderId);

// Execute the query
$stmt->execute();

// Retrieve the result set from the executed query
$result = $stmt->get_result();

// Fetch the order data as an associative array
$order = $result->fetch_assoc();

// Check if the order exists
if (!$order) {
    http_response_code(404); // Send HTTP status code 404 (Not Found)
    echo json_encode([
        'status' => 'error', // Response status
        'errorMsg' => 'Order not found' // Error message if order does not exist
    ]);
    exit; // Stop execution
}

// Determine if the current user has ADMIN privileges
$isAdmin = ($role === 'ADMIN');

// Check if the logged-in user is the assigned user of the order
$isOwner = ($order['assigned_to'] !== null && (int) $order['assigned_to'] === $userId);

// Determine the next assigned user
// If the order is currently unassigned, assign it to the current user
$nextAssignedTo = ($order['assigned_to'] === null) ? $userId : (int) $order['assigned_to'];

// Set the next order status to the current status by default
$nextStatus = $order['status'];

// Perform logic depending on the action requested
switch ($action) {

    case 'claim_pack': // Action to claim an order for packing

        // If user is not admin and the order is already assigned to someone else
        if (!$isAdmin && $order['assigned_to'] !== null && !$isOwner) {
            http_response_code(403); // Send HTTP status code 403 (Forbidden)
            echo json_encode([
                'status' => 'error',
                'errorMsg' => 'This order is already assigned to another user' // Prevent claiming someone else's order
            ]);
            exit;
        }

        $nextAssignedTo = $userId; // Assign the order to the current user
        $nextStatus = 'pending'; // Set order status to pending
        break;

    case 'pack': // Action to mark an order as packed

        // Prevent non-admin users from packing orders assigned to others
        if (!$isAdmin && $order['assigned_to'] !== null && !$isOwner) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'errorMsg' => 'You can only pack your own orders' // Only owner can pack
            ]);
            exit;
        }

        // If order is unassigned, assign it to the current user
        $nextAssignedTo = ($order['assigned_to'] === null) ? $userId : (int) $order['assigned_to'];

        $nextStatus = 'packed'; // Change order status to packed
        break;

    case 'unpack': // Action to revert a packed order back to pending

        // Only admins or the order owner can unpack
        if (!$isAdmin && !$isOwner) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'errorMsg' => 'You can only unpack your own orders'
            ]);
            exit;
        }

        // Maintain assigned user or assign to current user if none
        $nextAssignedTo = ($order['assigned_to'] === null) ? $userId : (int) $order['assigned_to'];

        $nextStatus = 'pending'; // Change order status back to pending
        break;
}

// Prepare SQL update query to modify assigned user and status
$update = $conn->prepare("UPDATE orders_header SET assigned_to = ?, status = ? WHERE id = ?");

// Bind parameters to the query (integer, string, integer)
$update->bind_param("isi", $nextAssignedTo, $nextStatus, $orderId);

// Execute the update query
$update->execute();

// Return a success response in JSON format
echo json_encode([
    'status' => 'success', // Indicates operation completed successfully
    'order_id' => $orderId, // The ID of the order that was updated
    'assigned_to' => $nextAssignedTo, // The user now assigned to the order
    'new_status' => $nextStatus // The updated status of the order
]);
?>