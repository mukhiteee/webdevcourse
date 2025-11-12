<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/comingsoon.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    header('Location: ../index.php?error=All fields required');
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
        header('Location: ../pages/comingsoon.php');
    } else {
    header('Location: ../index.php?error=Incorrect Password');
    }
} else {
    // echo json_encode(['success' => false, 'message' => 'No user found with that email.']);
    header('Location: ../index.php?error=No user found with that email');
}
$stmt->close();
$conn->close();
?>