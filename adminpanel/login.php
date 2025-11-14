<?php
session_start();
require_once '../backend/config.php'; // adjust path as needed

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username && $password) {
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($aid, $aname, $hash);

    if ($stmt->num_rows === 1) {
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $aname;
            $_SESSION['admin_id'] = $aid;
            header("Location: dashboard.php");
            exit;
        }
    }
    $stmt->close();
}

header("Location: index.php?error=Invalid+username+or+password");
exit;