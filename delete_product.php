<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id']) || !is_numeric($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
        exit();
    }

    $id = intval($data['id']);

    // Delete related images
    $img_stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
    $img_stmt->bind_param("i", $id);
    $img_stmt->execute();
    $img_stmt->close();

    // Delete related favorites
    $fav_stmt = $conn->prepare("DELETE FROM favorites WHERE product_id = ?");
    $fav_stmt->bind_param("i", $id);
    $fav_stmt->execute();
    $fav_stmt->close();

    // (Optional) Delete related reviews
    if ($conn->query("SHOW TABLES LIKE 'reviews'")->num_rows > 0) {
        $review_stmt = $conn->prepare("DELETE FROM reviews WHERE product_id = ?");
        $review_stmt->bind_param("i", $id);
        $review_stmt->execute();
        $review_stmt->close();
    }

    // Delete the product itself
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
