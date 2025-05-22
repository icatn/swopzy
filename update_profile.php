<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user'];
$query = "SELECT id FROM users WHERE email=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $fields = [];
    $params = [];
    $types = '';
    if ($username !== '') {
        $fields[] = "username=?";
        $params[] = $username;
        $types .= 's';
    }
    if ($email !== '') {
        $fields[] = "email=?";
        $params[] = $email;
        $types .= 's';
    }
    if ($password !== '') {
        $fields[] = "password=?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
        $types .= 's';
    }
    if ($fields) {
        $sql = "UPDATE users SET ".implode(", ", $fields)." WHERE id=?";
        $stmt = $conn->prepare($sql);
        $types .= 'i';
        $params[] = $user_id;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
        // If email changed, update session
        if ($email !== '') {
            $_SESSION['user'] = $email;
        }
        echo '<script>alert("Profile updated successfully!");window.location.href="profile.php";</script>';
        exit();
    }
}
$conn->close();
?>
