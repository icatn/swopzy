<?php
$successMsg = $errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if ($username && $email && $message) {
        $to = 'gamingkhalilo@gmail.com';
        $subject = 'Contact Form Message from ' . $username;
        $body = "Username: $username\nEmail: $email\nMessage:\n$message";
        $headers = "From: $email\r\nReply-To: $email\r\n";
        
        if (mail($to, $subject, $body, $headers)) {
            $successMsg = 'Your message has been sent successfully!';
        } else {
            $errorMsg = 'An error occurred while sending your message. Please try again later.';
        }
    } else {
        $errorMsg = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="home.css">
    <title>Contact Us | Swopzy</title>
</head>
<body style="background:rgba(39,39,39,0.94);color:#fff;">
    <div class="wrapper" style="min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;">
        <div style="max-width:600px;margin:60px auto 0 auto;padding:40px 30px;background:rgba(0,0,0,0.18);border-radius:18px;box-shadow:0 4px 18px rgba(0,0,0,0.13);">
            <h1 style="text-align:center;font-size:2.5rem;margin-bottom:18px;">Contact Us</h1>
            <p style="font-size:1.2rem;line-height:1.7;text-align:center;">Have questions, suggestions, or feedback? We would love to hear from you!<br><br>Email us at <a href="mailto:swopzy@gmail.com" style="color:#50bfff;text-decoration:underline;">swopzy@gmail.com</a> or use the form below.</p>
            <?php if ($successMsg): ?>
    <div style="background:#28a745;color:#fff;padding:12px;border-radius:8px;text-align:center;margin-bottom:10px;">
        <?= htmlspecialchars($successMsg) ?>
    </div>
<?php elseif ($errorMsg): ?>
    <div style="background:#dc3545;color:#fff;padding:12px;border-radius:8px;text-align:center;margin-bottom:10px;">
        <?= htmlspecialchars($errorMsg) ?>
    </div>
<?php endif; ?>
<form method="post" action="contact.php" style="margin-top:30px;display:flex;flex-direction:column;gap:16px;">
    <input type="text" name="username" placeholder="Your Name" required style="padding:10px;border-radius:8px;border:none;">
    <input type="email" name="email" placeholder="Your Email" required style="padding:10px;border-radius:8px;border:none;">
    <textarea name="message" placeholder="Your Message" rows="5" required style="padding:10px;border-radius:8px;border:none;"></textarea>
    <button type="submit" style="padding:12px 0;background:#007bff;color:#fff;border:none;border-radius:8px;font-size:1.1rem;cursor:pointer;">Send Message</button>
</form>
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

</body>
</html>
