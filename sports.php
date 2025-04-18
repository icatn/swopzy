<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$email = $_SESSION['user'];
$query = "SELECT id, username, email FROM users WHERE email=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $username, $email);
$stmt->fetch();
$stmt->close();

$product_query = "SELECT * FROM products WHERE category = 'Sports'";
$stmt = $conn->prepare($product_query);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" >
    <title></title>
</head>
<body>
<div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <img src="logo-swopzy.png" alt="Swopzy Logo">
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="home.php" class="link">Home</a></li>
                    <li><a href="sell.php" class="link">Sell_Products</a></li>
                    <li><a href="#" class="link active">Sports</a></li>
                </ul>
            </div>
            <div class="nav-button">
                <button class="btn" onclick="logout()">Logout</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>
        <section class="user-products">
            <h2>Sports Products</h2>
            <div class="products-list">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-content">
                            <div class="product-media">
                                <?php if (strpos($product['picture'], '.jpg') !== false || strpos($product['picture'], '.png') !== false || strpos($product['picture'], '.jpeg') !== false): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <?php elseif (strpos($product['picture'], '.mp4') !== false || strpos($product['picture'], '.webm') !== false): ?>
                                    <video src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" controls class="product-video"></video>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                                <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                                <p><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="product-actions">
                                    <button class="modify-btn" onclick="showModifyForm('<?php echo htmlspecialchars($product['id']); ?>')">More information</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
</html>