<?php
if ($password !== $confirm_password) {
    header('Location: ../index.php?error=' . urlencode('Passwords do not match'));
    exit();
}

if(strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6){
    header('Location: ../index.php?error=' . urlencode('Invalid input'));
    exit();
}

if($exists > 0){
    header('Location: ../index.php?error=' . urlencode('Email already registered'));
    exit();
}

// On general registration failure
if (!$stmt->execute()) {
    header('Location: ../index.php?error=' . urlencode('Registration failed'));
    exit();
}

// On success, redirect or display a success message
header('Location: ../index.php?success=' . urlencode('Registration successful! Log in below.'));
exit();
?>