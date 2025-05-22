<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$email = $_SESSION['user'];
// Get user id
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $username);
$stmt->fetch();
$stmt->close();
// Get favorite products
$query = "SELECT p.* FROM products p JOIN favorites f ON p.id = f.product_id WHERE f.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
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
    <link rel="stylesheet" href="style.css">
    <title>My Favorites | Swopzy</title>
    <style>
    .remove-favorite-btn {
        background: #ff4d4d;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
        margin-bottom: 8px;
        transition: background 0.2s;
    }
    .remove-favorite-btn:hover {
        background: #d93636;
    }
    .product-gallery {
        position: relative;
    }
    .product-image.main-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
    }
    .gallery-thumbs {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 8px;
    }
    .gallery-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
    }
    </style>
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
                <li><a href="favorite.php" class="link active">My Favorites</a></li>
                <li><a href="sell.php" class="link">Sell Products</a></li>
            </ul>
        </div>
        <div class="nav-button">
            <button class="btn" onclick="logout()">Logout</button>
        </div>
    </nav>
    <section class="user-products">
        <h2>My Favorite Products</h2>
        <div class="products-list">
            <?php if (empty($products)): ?>
                <p>You have not favorited any products yet.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-content">
                            <div class="product-media">
                                <a href="product.php?id=<?php echo $product['id']; ?>" style="display:block;text-decoration:none;color:inherit;">
                                <?php
                                // Only show main image (first image), no gallery
                                $conn = new mysqli("localhost", "root", "", "swopzy");
                                $img_q = $conn->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1");
                                $img_q->bind_param("i", $product['id']);
                                $img_q->execute();
                                $img_res = $img_q->get_result();
                                $main_img = $img_res->fetch_assoc();
                                $img_q->close();
                                $conn->close();
                                ?>
                                <?php if ($main_img): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($main_img['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <?php elseif (strpos($product['picture'], '.jpg') !== false || strpos($product['picture'], '.png') !== false || strpos($product['picture'], '.jpeg') !== false): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <?php elseif (strpos($product['picture'], '.mp4') !== false || strpos($product['picture'], '.webm') !== false): ?>
                                    <video src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" controls class="product-video"></video>
                                <?php endif; ?>
                                </a>
                            </div>
                            <div class="product-info">
                                <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration:none;color:inherit;">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                </a>
                                <p><strong>Contact:</strong> <?php echo !empty($product['number']) ? htmlspecialchars($product['number']) : 'Not provided'; ?></p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                                <p><strong>Price:</strong> da<?php echo htmlspecialchars($product['price']); ?></p>
                                <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['product_condition']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                <button class="remove-favorite-btn" onclick="removeFavorite(<?php echo $product['id']; ?>, this)">Remove from Favorites</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
<footer id="footer" style="background:rgba(39,39,39,0.92); color:#fff; padding:0 0 20px 0; text-align:center; font-size:17px; margin-top:0; box-shadow:0 -2px 18px rgba(0,0,0,0.10);">
    <div style="max-width:1200px; margin:auto; display:flex; flex-direction:column; align-items:center; gap:18px;">
        <div style="margin-bottom:10px;">
            <img src="logo-swopzy.png" alt="Swopzy Logo" style="height:45px; vertical-align:middle; filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));">
        </div>
        <div style="font-size:22px; font-weight:bold; letter-spacing:1px;">Swopzy</div>
        <div style="display:flex; gap:18px; font-size:18px; margin-bottom:8px;">
            <a href="contact.php" style="color:#50bfff; text-decoration:underline;">Contact Us</a>
            <span style="color:#fff;">|</span>
            <a href="about.php" style="color:#50bfff; text-decoration:underline;">About</a>
            <span style="color:#fff;">|</span>
            <a href="favorite.php" style="color:#50bfff; text-decoration:underline;">Favorites</a>
        </div>
        <div style="font-size:15px; color:#e0e0e0;">&copy; 2025 Swopzy. All rights reserved.</div>
        <div style="font-size:13px; color:#b0e0ff;">Designed by Swopzy Team</div>
    </div>
</footer>
<script>
function logout() {
    window.location.href = "logout.php";
}
function removeFavorite(productId, btn) {
    if (!confirm('Remove this product from your favorites?')) return;
    fetch('remove_favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + encodeURIComponent(productId)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove the product card from the DOM
            let card = btn.closest('.product-card');
            if (card) card.remove();
        } else {
            alert('Failed to remove favorite.');
        }
    })
    .catch(() => alert('Error removing favorite.'));
}
</script>
</body>
</html>
