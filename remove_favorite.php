<?php
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $conn = new mysqli("localhost", "root", "", "swopzy");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Connection failed']);
        exit();
    }
    $email = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    $product_id = intval($_POST['product_id']);
    $del_stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $del_stmt->bind_param("ii", $user_id, $product_id);
    $success = $del_stmt->execute();
    $del_stmt->close();
    $conn->close();
    echo json_encode(['success' => $success]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
