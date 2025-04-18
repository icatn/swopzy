<?php
session_start();
include 'db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT id, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id; // Set session

                echo "<script>alert('Login successful!'); window.location.href='profile.php';</script>";
                exit();
            } else {
                echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('User not found'); window.location.href='login.php';</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>
