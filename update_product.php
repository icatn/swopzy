<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false];

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get user ID
$email = $_SESSION['user'];
$query = "SELECT id FROM users WHERE email=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$condition = $_POST['product_condition'] ?? '';
$description = isset($_POST['description']) ? strval($_POST['description']) : '';

// Check required fields
if (!$id || !$name || !$price || !$condition) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Handle file upload if present
$picture = null;
if (!empty($_FILES['file']['name'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'mp4', 'webm'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $picture = $fileName;
            // Update or insert into product_images table
            $img_stmt = $conn->prepare("SELECT id FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1");
            $img_stmt->bind_param("i", $id);
            $img_stmt->execute();
            $img_stmt->store_result();
            if ($img_stmt->num_rows > 0) {
                $img_stmt->bind_result($img_id);
                $img_stmt->fetch();
                $img_stmt->close();
                $update_img = $conn->prepare("UPDATE product_images SET image=? WHERE id=?");
                $update_img->bind_param("si", $picture, $img_id);
                $update_img->execute();
                $update_img->close();
            } else {
                $img_stmt->close();
                $insert_img = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                $insert_img->bind_param("is", $id, $picture);
                $insert_img->execute();
                $insert_img->close();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit();
    }
}

// Update product
if ($picture) {
    $sql = "UPDATE products SET name=?, price=?, product_condition=?, description=?, picture=? WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssii", $name, $price, $condition, $description, $picture, $id, $user_id);
} else {
    $sql = "UPDATE products SET name=?, price=?, product_condition=?, description=? WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisiii", $name, $price, $condition, $description, $id, $user_id);
}

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['message'] = 'Database update failed';
}
$stmt->close();
$conn->close();

echo json_encode($response);
?>
