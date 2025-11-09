<?php
header('Content-Type: application/json');
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/dashboard.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate
if(strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6){
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
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
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit();
}

// Hash password and insert user
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $email, $hash);

if($stmt->execute()){
    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}
$stmt->close();
$conn->close();
?>