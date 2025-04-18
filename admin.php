<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = isset($_SESSION['user']) ? $_SESSION['user'] : '';
$query = "SELECT role FROM users WHERE email=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch(); // Fetch the role from the result set
$stmt->close();

if ($role !== 'admin') {
    header("Location: home.php"); // Redirect to home page if not admin
    exit();
}

// Modified query to select all products
$product_query = "SELECT * FROM products";
$stmt = $conn->prepare($product_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $stmt->error);
}

$products = [];
while ($row = $result->fetch_assoc()) {  // Returns an associative array of strings representing the fetched row
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
    <link rel="stylesheet" href="admin.css">
    <title>Admin Dashboard</title>
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
                    <li><a href="home.php" class="link">Home</a></li>
                    <li><a href="admin_dashboard.php" class="link active">Dashboard</a></li>
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
            <h2>All Products</h2>
            <div class="products-list">
                <?php if (empty($products)): ?>
                    <p>No products found.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" id="product-<?php echo htmlspecialchars($product['id']); ?>">
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
                                        <button class="accept-btn" onclick="acceptProduct(<?php echo htmlspecialchars($product['id']); ?>)">Accept</button>
                                        <button class="delete-btn" onclick="deleteProduct(<?php echo htmlspecialchars($product['id']); ?>)">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <script>
        function logout() {
            window.location.href = 'logout.php';
        }

        function myMenuFunction() {
            var x = document.getElementById("navMenu");
            if (x.style.top === "-800px") {
                x.style.top = "100px";
            } else {
                x.style.top = "-800px";
            }
        }

        function acceptProduct(id) {
            // Remove the product card from the DOM
            document.getElementById('product-' + id).remove();
        }

        function deleteProduct(id) {
            // Send an AJAX request to delete_product.php
            $.ajax({
                url: 'delete_product.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: id }),
                success: function(response) {
                    if (response.success) {
                        // Remove the product card from the DOM
                        document.getElementById('product-' + id).remove();
                        
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the product.');
                }
            });
        }
    </script>
</body>
</html>