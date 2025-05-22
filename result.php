<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = isset($_SESSION['user']) ? $_SESSION['user'] : '';
$fav_ids = [];
if ($email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    $fav_query = "SELECT product_id FROM favorites WHERE user_id = '$user_id'";
    $fav_result = $conn->query($fav_query);
    while ($row = $fav_result->fetch_assoc()) {
        $fav_ids[] = $row['product_id'];
    }
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Search Results</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="wrapper">
    <nav class="nav">
        <div class="nav-logo">
            <img src="logo-swopzy.png" alt="Swopzy Logo">
        </div>
        <div class="nav-menu" id="navMenu">
            <ul>
                <li><a href="logout.php" class="link">Logout</a></li>
                <li><a href="home.php" class="link">Home</a></li>
                <li><a href="favorite.php" class="link">Favorite</a></li>
                <li><a href="profile.php" class="link">Profile</a></li>
                <li><a href="sell.php" class="link">Add_Product</a></li>
                <li><a href="#" class="link" onclick="window.location.href='home.php#contact';">Contact</a></li>
                <li><a href="#" class="link" onclick="window.location.href='home.php#about';">About Us</a></li>
            </ul>
        </div>
        <div class="nav-menu-btn">
            <i class="bx bx-menu" onclick="toggleMenu()"></i>
        </div>
    </nav>
    <section class="user-products">
        <h2>Search Results</h2>
        <div class="products-list">
            <?php if (empty($products)): ?>
                <p>No products found matching your search.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration:none;color:inherit;">
                    <div class="product-card">
                        <div class="product-media">
                            <?php if (isset($product['picture']) && (strpos($product['picture'], '.jpg') !== false || strpos($product['picture'], '.png') !== false || strpos($product['picture'], '.jpeg') !== false)): ?>
                                <img src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" class="product-image">
                            <?php elseif (isset($product['picture']) && (strpos($product['picture'], '.mp4') !== false || strpos($product['picture'], '.webm') !== false)): ?>
                                <video src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" controls class="product-video"></video>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="favorite-star" data-product-id="<?php echo $product['id']; ?>">
                                <i class='bx <?php echo in_array($product['id'], $fav_ids) ? "bxs-star" : "bx-star"; ?>'></i>
                            </span>
                            <h3><?php echo htmlspecialchars($product['name'] ?? ''); ?></h3>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category'] ?? ''); ?></p>
                            <p><strong>Price:</strong> da<?php echo htmlspecialchars($product['price'] ?? ''); ?></p>
                            <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['product_condition'] ?? ''); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                        </div>
                    </div>
                    </a>
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
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    navMenu.classList.toggle('active');
}
$(document).ready(function() {
    $('.favorite-star').click(function(e) {
        e.stopPropagation();
        var star = $(this).find('i');
        var productId = $(this).data('product-id');
        $.post('favorite_toggle.php', { product_id: productId }, function(data) {
            if(data === 'added') {
                star.removeClass('bx-star').addClass('bxs-star');
            } else if(data === 'removed') {
                star.removeClass('bxs-star').addClass('bx-star');
            }
        });
    });
});
</script>
</body>
</html>
