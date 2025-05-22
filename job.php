<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Job</title>
    <style>
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            max-width: 90%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
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
                <li><a href="sell.php" class="link">Sell Products</a></li>
                <li><a href="#" class="link active">Job</a></li>
                <li><a href="#footer" class="link" onclick="scrollToFooter(event)">Contact Us</a></li>
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
        <h2>Job</h2>
        <div class="products-list">
            <?php 
            // Enable error reporting for debugging
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            session_start();
            $conn = new mysqli("localhost", "root", "", "swopzy", 3306);
            if ($conn->connect_error) {
                die("Connection failed (main): " . $conn->connect_error);
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

            // Fetch products with the phone number from the products table
            $product_query = "
                SELECT * 
                FROM products 
                WHERE category = 'Job'
            ";
            $stmt = $conn->prepare($product_query);
            $stmt->execute();
            $result = $stmt->get_result();

            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt->close();
            $conn->close();

            $fav_conn = new mysqli("localhost", "root", "", "swopzy", 3306);
            if ($fav_conn->connect_error) {
                die("Connection failed (fav): " . $fav_conn->connect_error);
            }
            $fav_query = "SELECT product_id FROM favorites WHERE user_id = '$user_id'";
            $fav_result = $fav_conn->query($fav_query);
            $fav_ids = [];
            while ($row = $fav_result->fetch_assoc()) {
                $fav_ids[] = $row['product_id'];
            }
            $fav_conn->close();
            ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration:none;color:inherit;">
                        <div class="product-content">
                            <div class="product-media">
                                <?php
                                $conn = new mysqli("localhost", "root", "", "swopzy");
                                $img_q = $conn->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1");
                                $img_q->bind_param("i", $product['id']);
                                $img_q->execute();
                                $img_res = $img_q->get_result();
                                $main_img = $img_res->fetch_assoc();
                                $img_q->close();
                                $conn->close();
                                ?>
                                <?php if ($main_img && !empty($main_img['image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($main_img['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <?php elseif (isset($product['picture']) && (strpos($product['picture'], '.jpg') !== false || strpos($product['picture'], '.png') !== false || strpos($product['picture'], '.jpeg') !== false)): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['picture']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <span class="favorite-star" data-product-id="<?php echo $product['id']; ?>">
                                    <i class='bx <?php echo in_array($product['id'], $fav_ids) ? "bxs-star" : "bx-star"; ?>'></i>
                                </span>
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p><strong>Contact:</strong> <?php echo !empty($product['number']) ? htmlspecialchars($product['number']) : 'Not provided'; ?></p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                                <p><strong>Price:</strong> da<?php echo htmlspecialchars($product['price']); ?></p>
                                <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['product_condition']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.favorite-star').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var star = $(this).find('i');
        var productId = $(this).data('product-id');
        $.post('favorite.php', { product_id: productId }, function(data) {
            if(data === 'added') {
                star.removeClass('bx-star').addClass('bxs-star');
            } else if(data === 'removed') {
                star.removeClass('bxs-star').addClass('bx-star');
            }
        });
    });
});
</script>

<script>
function myMenuFunction() {
    var i = document.getElementById("navMenu");
    if (i.className === "nav-menu") {
        i.className += " responsive";
    } else {
        i.className = "nav-menu";
    }
}

function logout() {
    window.location.href = "logout.php";
}

function scrollToFooter(event) {
    event.preventDefault();
    document.getElementById('footer').scrollIntoView({ behavior: 'smooth' });
}
</script>
</body>
</html>