<?php
require __DIR__ . '/database/DBConnection.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["status" => "error", "message" => "Missing ID"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, email=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $data['firstName'], $data['lastName'], $data['email'], $data['status'], $data['id']);
    
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