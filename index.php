<?php
session_start();
require_once 'backend/config.php';

if (isset($_SESSION['user_id'])) {
    header ('Location: pages/comingsoon.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Development For Beginners | Code With Mukhiteee</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="icon" href="" type="image/png">
<meta name="description" content="Learn web development from scratch. Join our beginner-friendly course, get hands-on tutorials, and connect with a vibrant learning community. Platform launching soon!">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Web Development for Beginners">
<meta name="twitter:description" content="Learn web development from scratch. Join our beginner-friendly course, get hands-on tutorials, and connect with a vibrant learning community. Platform launching soon!">
<meta name="twitter:image" content="https://yourdomain.com/banner.png">

<meta property="og:title" content="Web Development for Beginners">
<meta property="og:description" content="Learn web development from scratch. Join our beginner-friendly course, get hands-on tutorials, and connect with a vibrant learning community. Platform launching soon!">
<meta property="og:image" content="https://yourdomain.com/banner.png">
<meta property="og:url" content="https://yourdomain.com/">
<meta property="og:type" content="website">
</head>
<body>
        <div class="logo">Code <span>With</span> Mukhiteee</div>
    <div class="tagline">Master coding, one line at a time</div>

    <div class="auth-card">
        <div class="tabs">
            <button class="tab active" data-tab="login">Login</button>
            <button class="tab" data-tab="signup">Sign Up</button>
            <div class="tab-indicator" id="indicator"></div>
        </div>
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="error-msg" style="
    background-color: #8003;
    padding: 10px 30px;
    border-radius: 10px;
    align-self: center;
    margin-bottom: 15px;
    justify-self: center;
    text-align: center;">Invalid email or password.</div>';
        }
        ?>
        <div class="form-container">
            <form class="form active" id="loginForm" action="backend/login.php" method="POST">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" placeholder="your.email@example.com" required>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                </div>
                <a href="../pages/forgot_password.php" style="color: #10b859; font-weight: 600; text-decoration: none; text-align: right;">Forgot Password</a>
                <button type="submit" class="cta-button">Login</button>
                <div class="switch-link">
                    Don't have an account? <a id="switchToSignup">Sign Up</a>
                </div>
            </form>

            <form class="form" id="signupForm" action="backend/register.php" method="POST">
                <div class="form-group">
                    <label for="signupName">Name</label>
                    <input type="text" name="name" id="signupName" placeholder="Your full name" required>
                </div>
                <div class="form-group">
                    <label for="signupEmail">Email</label>
                    <input type="email" name="email" id="signupEmail" placeholder="your.email@example.com" required>
                </div>
                <div class="form-group">
                    <label for="signupPassword">Password</label>
                    <input type="password" name="password" id="signupPassword" placeholder="Create a strong password" required>
                </div>
                <button type="submit" class="cta-button">Sign Up</button>
                <div class="switch-link">
                    Already have an account? <a id="switchToLogin">Login</a>
                </div>
            </form>
        </div>
    </div>
    <script src="scripts/main.js"></script>
</body>
</html>