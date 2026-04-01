<?php
session_start();
require __DIR__ . '/database/DBConnection.php';
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}


// Check if user is ADMIN
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    $orderId = $data['orderId'] ?? null;
    $newAssignedTo = $data['assignedTo'] ?? null;

    if (!$orderId) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "errorMsg" => "Order ID is required"
        ]);
        exit;
    }

    // Update assignment (allow NULL to unassign)
    $stmt = $conn->prepare("UPDATE orders_header SET assigned_to = ? WHERE id = ?");
    $stmt->bind_param("ii", $newAssignedTo, $orderId);

    if ($stmt->execute()) {
        // Fetch updated order with new assignment details
        $result = $conn->query("
            SELECT 
                oh.id,
                oh.ext_id,
                oh.assigned_to,
                u.firstName,
                u.lastName
            FROM orders_header oh
            LEFT JOIN users u ON oh.assigned_to = u.id
            WHERE oh.id = $orderId
        ");
        
        $updatedOrder = $result->fetch_assoc();
        
        echo json_encode([
            "status" => "success",
            "message" => "Assignment updated successfully",
            "order" => $updatedOrder
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "errorMsg" => "Failed to update assignment: " . $stmt->error
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "errorMsg" => $e->getMessage()
    ]);
}
?>
