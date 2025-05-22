<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$user_id = 0;
if (isset($_SESSION['user'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['user']);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id > 0 && $product_id > 0) {
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
    $stmt->execute();
    $stmt->close();
    header("Location: product.php?id=$product_id");
    exit();
}

// Fetch product reviews
$reviews = [];
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>
