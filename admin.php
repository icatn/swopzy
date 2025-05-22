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
$stmt->fetch();
$stmt->close();

if ($role !== 'admin') {
    header("Location: home.php");
    exit();
}

$product_query = "SELECT * FROM products";
$stmt = $conn->prepare($product_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
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
                                <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $product['picture'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                <?php elseif (preg_match('/\.(mp4|webm)$/i', $product['picture'])): ?>
                                    <video src="uploads/<?php echo htmlspecialchars($product["picture"]); ?>" controls class="product-video"></video>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p><strong>ID:</strong> <?php echo htmlspecialchars($product['id']); ?></p>
                                <p><strong>User ID:</strong> <?php echo htmlspecialchars($product['user_id']); ?></p>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                                <p><strong>Price:</strong> da<?php echo htmlspecialchars($product['price']); ?></p>
                                <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['product_condition']); ?></p>
                                <p><strong>Picture:</strong> <?php echo htmlspecialchars($product['picture']); ?></p>
                                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($product['number']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
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
        x.style.top = (x.style.top === "-800px") ? "100px" : "-800px";
    }

    function acceptProduct(id) {
        document.getElementById('product-' + id).remove(); // placeholder
    }

    function deleteProduct(id) {
        console.log("Attempting to delete product with ID:", id);
        $.ajax({
            url: 'delete_product.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: id }),
            dataType: 'json',
            cache: false,
            success: function (response) {
                if (response.success) {
                    document.getElementById('product-' + id).remove();
                } else {
                    alert('Failed to delete product: ' + (response.error || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred while deleting the product.');
            }
        });
    }
</script>
</body>
</html>
