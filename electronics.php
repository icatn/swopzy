<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Electronics</title>
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
                <li><a href="#" class="link active">Electronics</a></li>
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
        <h2>Electronics</h2>
        <div class="products-list">
            <?php 
            session_start();
            $conn = new mysqli("localhost", "root", "", "swopzy", 3306);
            if ($conn->connect_error) {
                die("Connection failed (main): " . $conn->connect_error);
            }

            if (!isset($_SESSION['user'])) {
                header("Location: page1.php"); // Redirect to login page if not logged in
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

            $product_query = "
                SELECT * 
                FROM products 
                WHERE category = 'Electronics'
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

            // Fetch user's favorite product IDs
            $fav_conn = new mysqli("localhost", "root", "", "swopzy", 3306);
            if ($fav_conn->connect_error) {
                die("Connection failed (fav): " . $fav_conn->connect_error);
            }
            $fav_ids = [];
            $fav_stmt = $fav_conn->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
            $fav_stmt->bind_param("i", $user_id);
            $fav_stmt->execute();
            $fav_result = $fav_stmt->get_result();
            while ($fav_row = $fav_result->fetch_assoc()) {
                $fav_ids[] = $fav_row['product_id'];
            }
            $fav_stmt->close();
            $fav_conn->close();
            ?>
            <?php 
            // Enable error reporting for debugging
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            ?>
            <?php foreach (
                isset($products) ? $products : [] as $product): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration:none;color:inherit;">
                        <div class="product-content">
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
                                <!-- Display the phone number -->
                                <p><strong>Contact:</strong> 
                                    <?php echo !empty($product['number']) ? htmlspecialchars($product['number']) : 'Not provided'; ?>
                                </p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category'] ?? ''); ?></p>
                                <p><strong>Price:</strong> da<?php echo htmlspecialchars($product['price'] ?? ''); ?></p>
                                <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['product_condition'] ?? ''); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($products)) {
                echo '<p style="color:red;">No products found in this category.</p>';
            }
            ?>
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