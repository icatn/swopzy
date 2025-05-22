<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $query = "SELECT * FROM users WHERE email=? AND password=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user'] = $email; // Set the session when logged in
            $_SESSION['role'] = $user['role']; // Store the user's role in the session

            if ($user['role'] == 'admin') {
                header("Location: admin.php"); // Redirect to admin page if role is admin
            } else {
                header("Location: home.php"); // Redirect to home page for other users
            }
            exit();
        } else {
            echo "<script>alert('Invalid credentials');</script>";
        }
        $stmt->close();
    }

    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, '')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>swopzy | Login & Registration</title>
</head>
<body>
<div class="wrapper">
    <nav class="nav">
        <div class="nav-logo">
            <img src="logo-swopzy.png" alt="Swopzy Logo">
        </div>
        <div class="nav-menu" id="navMenu">
            <ul>
                <li><a href="#" class="link active">log_in</a></li>
                <li><a href="#" class="link" onclick="scrollToFooter(event)">Services</a></li>
                
            </ul>
        </div>
        <div class="nav-button">
            <button class="btn white-btn" id="loginBtn" onclick="login()">Sign_In</button>
            <button class="btn" id="registerBtn" onclick="register()">Sign_Up</button>
        </div>
        <div class="nav-menu-btn">
            <i class="bx bx-menu" onclick="myMenuFunction()"></i>
        </div>
    </nav>

    <div class="form-box">
        <div class="login-container" id="login">
            <div class="top">
                <span>Don't have an account? <a href="#" onclick="register()">Sign Up</a></span>
                <header>Login</header>
            </div>
            <form method="POST" action="">
                <div class="input-box">
                    <input type="text" class="input-field" placeholder="Email" name="email" required>
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" placeholder="Password" name="password" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Sign In" name="login">
                </div>
            </form>
        </div>

        <div class="register-container" id="register">
            <div class="top">
                <span>Have an account? <a href="#" onclick="login()">Login</a></span>
                <header>Sign Up</header>
            </div>
            <form method="POST" action="">
                <div class="input-box">
                    <input type="text" class="input-field" placeholder="Username" name="username" required>
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" placeholder="Email" name="email" required>
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" placeholder="Password" name="password" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Register" name="register">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function myMenuFunction() {
    var i = document.getElementById("navMenu");
    if(i.className === "nav-menu") {
        i.className += " responsive";
    } else {
        i.className = "nav-menu";
    }
}
var x = document.getElementById("login");
var y = document.getElementById("register");
function login() {
    x.style.left = "4px";
    y.style.right = "-520px";
    x.style.opacity = 1;
    y.style.opacity = 0;
}
function register() {
    x.style.left = "-510px";
    y.style.right = "5px";
    x.style.opacity = 0;
    y.style.opacity = 1;
}
</script>
<script>
function scrollToFooter(event) {
    event.preventDefault();
    var footer = document.getElementById('footer');
    if (footer) {
        footer.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>

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
</body>
</html>