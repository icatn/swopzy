<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_POST['product_id'])) {
    exit('error');
}
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) exit('error');
$email = $_SESSION['user'];
// Get user id
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
$product_id = intval($_POST['product_id']);
// Check if already favorited
$check = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    // Remove favorite
    $del = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    $del->close();
    echo 'removed';
} else {
    // Add favorite
    $add = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    $add->bind_param("ii", $user_id, $product_id);
    $add->execute();
    $add->close();
    echo 'added';
}
$check->close();
$conn->close();
