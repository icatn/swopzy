<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
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
    <title>Product Details</title>
    <style>
        .product-detail-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 0;
        }
        .product-detail-image {
            width: 350px;
            height: 350px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            margin-bottom: 30px;
        }
        .product-image-slider {
            position: relative;
            width: 350px;
            margin-bottom: 30px;
        }
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(40,40,40,0.18);
            border: none;
            font-size: 1.2rem;
            padding: 2px 9px;
            cursor: pointer;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            color: #fff;
            transition: background 0.2s, transform 0.2s;
            z-index: 2;
        }
        .slider-btn:hover {
            background: rgba(40,40,40,0.32);
            transform: translateY(-50%) scale(1.10);
            color: #ffe066;
        }
        .slider-btn.left { left: 8px; }
        .slider-btn.right { right: 8px; }
        .product-detail-info {
            width: 100%;
            max-width: 500px;
            background: rgba(255,255,255,0.9);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .product-detail-info h2 {
            margin-bottom: 20px;
        }
        .product-detail-info p {
            margin: 10px 0;
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
                <li><a href="logout.php" class="link">Logout</a></li>
                <li><a href="home.php" class="link">Home</a></li>
                <li><a href="favorite.php" class="link">Favorite</a></li>
                <li><a href="profile.php" class="link">Profile</a></li>
                <li><a href="sell.php" class="link">Add_Product</a></li>
            </ul>
        </div>
        <div class="nav-menu-btn">
            <i class="bx bx-menu" onclick="toggleMenu()"></i>
        </div>
    </nav>
    <section class="product-detail-container">
        <?php if ($product): ?>
            <?php
            // Fetch all images for the product
            $conn = new mysqli("localhost", "root", "", "swopzy");
            $img_q = $conn->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC");
            $img_q->bind_param("i", $product['id']);
            $img_q->execute();
            $img_res = $img_q->get_result();
            $images = [];
            while ($img_row = $img_res->fetch_assoc()) {
                $images[] = $img_row['image'];
            }
            $img_q->close();
            $conn->close();
            ?>
            <div class="product-image-slider">
                <?php if (count($images) > 0): ?>
                    <?php foreach ($images as $idx => $img): ?>
                        <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-detail-image slider-img" style="display:<?php echo $idx === 0 ? 'block' : 'none'; ?>;">
                    <?php endforeach; ?>
                    <button id="prevBtn" class="slider-btn left" onclick="slideImage(-1)">&#8592;</button>
                    <button id="nextBtn" class="slider-btn right" onclick="slideImage(1)">&#8594;</button>
                <?php elseif (isset($product['picture']) && (strpos($product['picture'], '.jpg') !== false || strpos($product['picture'], '.png') !== false || strpos($product['picture'], '.jpeg') !== false)): ?>
                    <img src="uploads/<?php echo htmlspecialchars($product['picture']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-detail-image">
                <?php elseif (isset($product['picture']) && (strpos($product['picture'], '.mp4') !== false || strpos($product['picture'], '.webm') !== false)): ?>
                    <video src="uploads/<?php echo htmlspecialchars($product['picture']); ?>" controls class="product-detail-image"></video>
                <?php endif; ?>
            </div>
            <script>
            let currentSlide = 0;
            const images = document.querySelectorAll('.slider-img');
            function showSlide(idx) {
                if (!images.length) return;
                currentSlide = (idx + images.length) % images.length;
                images.forEach((img, i) => img.style.display = (i === currentSlide ? 'block' : 'none'));
            }
            function slideImage(dir) {
                showSlide(currentSlide + dir);
            }
            // Optional: Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if(e.key === 'ArrowLeft') slideImage(-1);
                if(e.key === 'ArrowRight') slideImage(1);
            });
            // On page load, show first image
            showSlide(0);
            </script>
            <div class="product-detail-info">
                <?php
                // Favorite logic
                $fav = false;
                $user_id = 0;
                if (isset($_SESSION['user'])) {
                    $conn = new mysqli("localhost", "root", "", "swopzy");
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->bind_param("s", $_SESSION['user']);
                    $stmt->execute();
                    $stmt->bind_result($user_id);
                    $stmt->fetch();
                    $stmt->close();
                    if ($user_id && $product_id) {
                        $fav_check = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
                        $fav_check->bind_param("ii", $user_id, $product_id);
                        $fav_check->execute();
                        $fav_check->store_result();
                        $fav = $fav_check->num_rows > 0;
                        $fav_check->close();
                    }
                    $conn->close();
                }
                ?>
                <div style="display:flex;align-items:center;gap:12px;">
                    <h2 style="margin-bottom:0;"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <span class="favorite-star" data-product-id="<?php echo $product['id']; ?>" style="font-size:2.1rem;cursor:pointer;vertical-align:middle;">
                        <i class='bx <?php echo $fav ? "bxs-star" : "bx-star"; ?>'></i>
                    </span>
                </div>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                <p><strong>Price:</strong> da<?php echo htmlspecialchars($product['price']); ?></p>
                <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['product_condition']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($product['number']); ?></p>
                <div style="margin-top:20px;">
                    <button onclick="shareOn('facebook')" class="btn" style="background:#3b5998;color:#fff;margin-right:8px;"><i class='bx bxl-facebook'></i> Facebook</button>
                    <button onclick="shareOn('twitter')" class="btn" style="background:#1da1f2;color:#fff;margin-right:8px;"><i class='bx bxl-twitter'></i> Twitter</button>
                    <button onclick="shareOn('whatsapp')" class="btn" style="background:#25d366;color:#fff;"><i class='bx bxl-whatsapp'></i> WhatsApp</button>
                </div>
            </div>
            <div class="product-reviews" style="margin-top:40px;max-width:500px;width:100%;">
                <h3>Reviews & Ratings</h3>
                <?php
                $conn = new mysqli("localhost", "root", "", "swopzy");
                $reviews = [];
                $avg_rating = 0;
                $review_count = 0;
                if ($product_id > 0) {
                    $stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $sum = 0;
                    while ($row = $result->fetch_assoc()) {
                        $reviews[] = $row;
                        $sum += $row['rating'];
                    }
                    $review_count = count($reviews);
                    $avg_rating = $review_count > 0 ? round($sum / $review_count, 2) : 0;
                    $stmt->close();
                }
                $conn->close();
                ?>
                <div style="margin-bottom:15px;">
                    <strong>Average Rating:</strong> <?php echo $avg_rating; ?> / 5
                    <?php for ($i=1; $i<=5; $i++) echo $i <= round($avg_rating) ? "<i class='bx bxs-star' style='color:gold;'></i>" : "<i class='bx bx-star'></i>"; ?>
                    (<?php echo $review_count; ?> reviews)
                </div>
                <?php if (isset($_SESSION['user'])): ?>
                <form action="reviews.php?product_id=<?php echo $product_id; ?>" method="POST" style="margin-bottom:20px;background:#f9f9f9;padding:15px;border-radius:7px;">
                    <label><strong>Your Rating:</strong>
                        <select name="rating" required style="margin-left:10px;">
                            <option value="">Select</option>
                            <?php for ($i=5; $i>=1; $i--): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </label>
                    <br>
                    <label><strong>Your Review:</strong>
                        <textarea name="review" rows="2" style="width:100%;margin-top:6px;" required></textarea>
                    </label>
                    <br>
                    <button type="submit" class="btn" style="background:#28a745;color:#fff;">Submit Review</button>
                </form>
                <?php endif; ?>
                <?php if ($review_count > 0): ?>
                    <?php foreach ($reviews as $r): ?>
                        <div style="background:#fff;padding:10px 15px;border-radius:7px;margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,0.07);">
                            <div><strong><?php echo htmlspecialchars($r['username']); ?></strong> - <?php for ($i=1; $i<=5; $i++) echo $i <= $r['rating'] ? "<i class='bx bxs-star' style='color:gold;'></i>" : "<i class='bx bx-star'></i>"; ?> <span style="font-size:12px;color:#888;">(<?php echo date('Y-m-d', strtotime($r['created_at'])); ?>)</span></div>
                            <div><?php echo nl2br(htmlspecialchars($r['review'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Product not found.</p>
        <?php endif; ?>
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
    function toggleMenu() {
        const navMenu = document.getElementById('navMenu');
        navMenu.classList.toggle('active');
    }
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
    function shareOn(platform) {
        var url = encodeURIComponent(window.location.href);
        var text = encodeURIComponent(document.title);
        if (platform === 'facebook') {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, '_blank');
        } else if (platform === 'twitter') {
            window.open('https://twitter.com/intent/tweet?url=' + url + '&text=' + text, '_blank');
        } else if (platform === 'whatsapp') {
            window.open('https://api.whatsapp.com/send?text=' + text + '%20' + url, '_blank');
        }
    }
    </script>
</body>
</html>
