<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "errorMsg" => "Admin access required"
    ]);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["status" => "error", "message" => "Missing ID"]);
        exit;
    }

    $id = $data['id'];
    $fName = $data['firstName'];
    $lName = $data['lastName'];
    $email = $data['email'];
    $status = $data['status'];
    $password = isset($data['password']) ? trim($data['password']) : '';

    if (!empty($password)) {
        // hashing new password (if changed)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, email=?, status=?, password=? WHERE id=?");
        $stmt->bind_param("sssssi", $fName, $lName, $email, $status, $hashedPassword, $id);
        
    } else {
        $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, email=?, status=? WHERE id=?");
        $stmt->bind_param("ssssi", $fName, $lName, $email, $status, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "redirect" => "a-profiles.html"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT id, firstName, lastName, email, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
    exit;
}
