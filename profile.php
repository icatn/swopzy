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

$product_query = "SELECT * FROM products WHERE user_id = ?";
$stmt = $conn->prepare($product_query);
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Profile | Swopzy</title>
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
                    <li><a href="#" class="link active">Profile</a></li>
                </ul>
            </div>
            <div class="nav-button">
                <button class="btn" onclick="logout()">Logout</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <div class="profile-container">
            <section class="user-info">
                <h2>Your Information</h2>
                <div id="profile-view">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <button class="btn" onclick="showEditProfile()" style="background:#007bff;color:#fff;">Edit Profile</button>
                </div>
                <form id="profile-edit-form" action="update_profile.php" method="POST" style="display:none;flex-direction:column;gap:10px;margin-top:10px;">
                    <label><strong>Username:</strong>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </label>
                    <label><strong>Email:</strong>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </label>
                    <label><strong>New Password:</strong>
                        <input type="password" name="password" placeholder="Leave blank to keep current password">
                    </label>
                    <div style="display:flex;gap:10px;">
                        <button type="submit" class="btn" style="background:#28a745;color:#fff;">Save</button>
                        <button type="button" class="btn" onclick="cancelEditProfile()" style="background:#dc3545;color:#fff;">Cancel</button>
                    </div>
                </form>
            </section>

            <section class="user-products">
                <h2>Your Products</h2>
                <div class="products-list">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-content">
                                <div class="product-media">
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
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                                    <p><strong>Price:</strong> da<?php echo htmlspecialchars($product['price']); ?></p>
                                    <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['product_condition']); ?></p>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                    <div class="product-actions">
                                        <button class="modify-btn" onclick="showModifyForm('<?php echo htmlspecialchars($product['id']); ?>')">Modify</button>
                                        <button class="delete-btn" onclick="deleteProduct('<?php echo htmlspecialchars($product['id']); ?>')">Delete</button>
                                    </div>
                                </div>
                            </div>
                            <div class="modify-form-container" id="modify-form-<?php echo htmlspecialchars($product['id']); ?>" style="display: none;">
                                <input type="text" id="name-<?php echo htmlspecialchars($product['id']); ?>" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Product Name">
                                <input type="number" id="price-<?php echo htmlspecialchars($product['id']); ?>" value="<?php echo htmlspecialchars($product['price']); ?>" placeholder="Price">
                                <select id="condition-<?php echo htmlspecialchars($product['id']); ?>">
                                    <option value="new" <?php if ($product['product_condition'] == 'new') echo 'selected'; ?>>New</option>
                                    <option value="used like new" <?php if ($product['product_condition'] == 'used like new') echo 'selected'; ?>>Used Like New</option>
                                    <option value="bad" <?php if ($product['product_condition'] == 'bad') echo 'selected'; ?>>Bad</option>
                                </select>
                                <textarea id="description-<?php echo htmlspecialchars($product['id']); ?>" placeholder="Product Description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                                <input type="file" id="file-<?php echo htmlspecialchars($product['id']); ?>" accept="image/*,video/*" onchange="previewFile('<?php echo htmlspecialchars($product['id']); ?>')">
                                <div id="preview-<?php echo htmlspecialchars($product['id']); ?>" class="preview-container"></div>
                                <button class="save-btn" onclick="saveProduct('<?php echo htmlspecialchars($product['id']); ?>')">Save</button>
                                <button class="cancel-btn" onclick="hideModifyForm('<?php echo htmlspecialchars($product['id']); ?>')">Cancel</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
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

        function showModifyForm(id) {
            document.getElementById(`modify-form-${id}`).style.display = "block";
        }

        function hideModifyForm(id) {
            document.getElementById(`modify-form-${id}`).style.display = "none";
        }

        function previewFile(id) {
            const fileInput = document.getElementById(`file-${id}`);
            const previewContainer = document.getElementById(`preview-${id}`);
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    let mediaPreview;
                    if (file.type.startsWith("image/")) {
                        mediaPreview = `<img src="${e.target.result}" class="preview-image">`;
                    } else if (file.type.startsWith("video/")) {
                        mediaPreview = `<video src="${e.target.result}" controls class="preview-video"></video>`;
                    }
                    previewContainer.innerHTML = mediaPreview;
                };
                reader.readAsDataURL(file);
            }
        }

        function saveProduct(id) {
            const newName = document.getElementById(`name-${id}`).value;
            const newPrice = document.getElementById(`price-${id}`).value;
            const newCondition = document.getElementById(`condition-${id}`).value;
            const newDescription = document.getElementById(`description-${id}`).value;
            const fileInput = document.getElementById(`file-${id}`);
            const file = fileInput.files[0];

            if (newName && newPrice && newCondition) {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('name', newName);
                formData.append('price', newPrice);
                formData.append('product_condition', newCondition);
                formData.append('description', newDescription);
                if (file) {
                    formData.append('file', file);
                }

                fetch('update_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product updated successfully');
                        location.reload();
                    } else {
                        alert('Error updating product');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch('delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product deleted successfully');
                        location.reload();
                    } else {
                        alert('Error deleting product');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function showEditProfile() {
            document.getElementById('profile-view').style.display = 'none';
            document.getElementById('profile-edit-form').style.display = 'flex';
        }

        function cancelEditProfile() {
            document.getElementById('profile-edit-form').style.display = 'none';
            document.getElementById('profile-view').style.display = 'block';
        }
    </script>
</body>
</html>