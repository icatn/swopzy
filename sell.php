<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user'])) {
    header("Location: page1.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sell'])) {
    $user_email = $_SESSION['user'];
    $user_query = "SELECT id FROM users WHERE email=?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("s", $user_email); // Link the input value to the query
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch(); // Fetch the data
    $stmt->close();

    $name = $_POST['productName'];
    $category = $_POST['productCategory'];
    $price = $_POST['productPrice'];
    $condition = $_POST['productCondition'];
    $phone_number = $_POST['phoneNumber']; 
    $description = $_POST['productDescription'];

    // Handle file upload
    $target_dir = "uploads/";
    if (!empty($_FILES['productMedia']['name'][0])) {
        $picture_names = [];
        foreach ($_FILES['productMedia']['name'] as $idx => $filename) {
            $target_file = $target_dir . basename($filename);
            if (move_uploaded_file($_FILES["productMedia"]["tmp_name"][$idx], $target_file)) {
                $picture_names[] = $filename;
            }
        }
        // Insert product first
        $sql = "INSERT INTO products (user_id, name, category, price, product_condition, picture, number, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $main_picture = $picture_names[0];
        $stmt->bind_param("isssssss", $user_id, $name, $category, $price, $condition, $main_picture, $phone_number, $description);
        if ($stmt->execute()) {
            $product_id = $stmt->insert_id;
            // Insert all images into product_images table
            $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
            if ($img_stmt) {
                foreach ($picture_names as $img) {
                    $img_stmt->bind_param("is", $product_id, $img);
                    $img_stmt->execute();
                }
                $img_stmt->close();
            } else {
                echo "Error preparing image insert statement: " . $conn->error;
            }
            echo "<script>alert('New product added successfully');</script>";
            header("Location: profile.php");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading files.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Sell Products | Swopzy</title>
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
                <li><a href="#" class="link active">Sell Products</a></li>
            </ul>
        </div>
        <div class="nav-button">
            <button class="btn" onclick="logout()">Logout</button>
        </div>
        <div class="nav-menu-btn">
            <i class="bx bx-menu" onclick="myMenuFunction()"></i>
        </div>
    </nav>

    <section id="sell" class="sell-section">
        <h2>Sell Your Products</h2>
        <form method="POST" action="" id="sellForm" class="sell-form" enctype="multipart/form-data">
            <div class="input-box">
                <input type="text" name="productName" id="productName" class="input-field" placeholder="Product Name" required>
                <i class="bx bx-tag"></i>
            </div>
            <div class="input-box">
                <select name="productCategory" id="productCategory" class="input-field" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Fashion">Fashion</option>
                    <option value="Books">Books</option>
                    <option value="Home">Home & Living</option>
                    <option value="Cars">Cars</option>
                    <option value="Job">Job Application</option>
                    <option value="Sports">Sports</option>
                    <option value="travel">Travel and tourism</option>
                    <option value="Health">Health</option>
                    <option value="Gardening">Gardening and farmings</option>
                    <option value="Education">Education</option>
                    <option value="Kids">Kids clothes</option>
                    <option value="Women">Women clothes</option>
                    <option value="Men">Men clothes</option>
                </select>
                <i class="bx bx-category"></i>
            </div>
            <div class="input-box">
                <input type="number" name="productPrice" id="productPrice" class="input-field" placeholder="Price (in da)" step="10" required>
                <i class="bx bx-dollar"></i>
            </div>
            <div class="input-box">
                <select name="productCondition" id="productCondition" class="input-field" required>
                    <option value=""  disabled selected>Select Condition</option>
                    <option value="new" style="color: black;">New</option>
                    <option value="used like new" style="color: black;">Used Like New</option>
                    <option value="bad" style="color: black;">Bad</option>
                </select>
                <i class="bx bx-info-circle"></i>
            </div>
            <div class="input-box">
                <input type="file" name="productMedia[]" id="productMedia" class="input-field" accept="image/*,video/*" multiple required>
                <i class="bx bx-image-add"></i>
            </div>
            <div class="input-box">
                <textarea name="productDescription" id="productDescription" class="input-field" placeholder="Product Description" rows="3" required></textarea>
                <i class="bx bx-message-alt-detail"></i>
            </div>
            <div class="input-box">
                <input type="number" name="phoneNumber" id="phoneNumber" class="input-field" placeholder="Phone Number" required>
                <i class="bx bx-phone"></i>
            </div>
            <div class="input-box">
                <button type="submit" name="sell" class="submit">Submit Product</button>
            </div>
        </form>
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
</script>
</body>
</html>