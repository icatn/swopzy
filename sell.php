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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sell'])) {
    $user_email = $_SESSION['user'];
    $user_query = "SELECT id FROM users WHERE email=?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("s", $user_email);//ربط القيمة المدخلة بالاستعلام
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch(); //جلب البيانات
    $stmt->close();

    $name = $_POST['productName'];
    $category = $_POST['productCategory'];
    $price = $_POST['productPrice'];
    $description = $_POST['productDescription'];
    $picture = $_FILES['productMedia']['name'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["productMedia"]["name"]);

    // Check if uploads directory exists, if not create it
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES["productMedia"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (user_id, name, category, price, description, picture)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssds", $user_id, $name, $category, $price, $description, $picture);

        if ($stmt->execute()) {
            echo "<script>alert('New product added successfully');</script>";
            header("Location: profile.php");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading file.";
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
                <textarea name="productDescription" id="productDescription" class="input-field" placeholder="Description" rows="4" required></textarea>
                <i class="bx bx-edit-alt"></i>
            </div>
            <div class="input-box">
                <input type="file" name="productMedia" id="productMedia" class="input-field" accept="image/*,video/*" required>
                <i class="bx bx-image-add"></i>
            </div>
            <div class="input-box">
                <button type="submit" name="sell" class="submit">Submit Product</button>
            </div>
        </form>
    </section>
</div>

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