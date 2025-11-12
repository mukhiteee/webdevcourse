<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/dashboard.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm'] ?? '';

if ($password !== $confirm_password) {
    header('Location: ../index.php?error=' . urlencode('Passwords do not match'));
    exit();
}

// Validate
if(strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6){
    header('Location: ../index.php?error=' . urlencode('Invalid input'));
    exit();
}

// Check for existing email
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email=?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($exists);
$stmt->fetch();
$stmt->close();

if($exists > 0){
    header('Location: ../index.php?error=' . urlencode('Email already registered'));
    exit();
}

// Hash password and insert user
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $email, $hash);

if($stmt->execute()){
    header('Location: ../index.php?success=' . urlencode('Registration successful login below'));
} else {
    header('Location: ../index.php?error=' . urlencode('Registration failed. Try again'));
}
$stmt->close();
$conn->close();
?>