<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/dashboard.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields required.']);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($id, $name, $hashed);
    $stmt->fetch();
    if (password_verify($password, $hashed)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['name'] = $name;
        header('Location: ../pages/dashboard.php');
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    }
} else {
    // echo json_encode(['success' => false, 'message' => 'No user found with that email.']);
    header('Location: ../index.php?echo=No user found with that email');
}
$stmt->close();
$conn->close();
?>