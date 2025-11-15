<?php
session_start();
require_once '../backend/config.php';
if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: index.php');
    exit;
}
$admin_username = ($_SESSION['admin_username']);
// Handle CRUD
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $reset_token_hash = trim($_POST['reset_token_hash'] ?? '');
    $reset_token_expires = trim($_POST['reset_token_expires'] ?? null);
    if ($name && $email && $password) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, reset_token_hash, reset_token_expires) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $name, $email, $password, $reset_token_hash, $reset_token_expires);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: users.php");
    exit;
}
if (isset($_POST['update_user'])) {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // blank means don't change
    $reset_token_hash = trim($_POST['reset_token_hash'] ?? '');
    $reset_token_expires = trim($_POST['reset_token_expires'] ?? null);
    if ($id && $name && $email) {
        if ($password !== '') {
            $passhash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=?, reset_token_hash=?, reset_token_expires=? WHERE id=?");
            $stmt->bind_param('sssssi', $name, $email, $passhash, $reset_token_hash, $reset_token_expires, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, reset_token_hash=?, reset_token_expires=? WHERE id=?");
            $stmt->bind_param('ssssi', $name, $email, $reset_token_hash, $reset_token_expires, $id);
        }
        $stmt->execute();
        $stmt->close();
    }
    header("Location: users.php");
    exit;
}
if (isset($_POST['delete_user'])) {
    $id = intval($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: users.php");
    exit;
}
$users = [];
$res = $conn->query("SELECT * FROM users ORDER BY id DESC");
while ($row = $res->fetch_assoc()) $users[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management | Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../styles/admindash.css">
  <link rel="stylesheet" href="../styles/users.css">
</head>

<body>
<div class="dashboard">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo-section">
      <div class="logo"><h1>Admin Panel</h1></div>
    </div>
    <div class="admin-profile">
      <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'AD',0,1)); ?></div>
      <div class="admin-info">
        <div class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></div>
        <div class="admin-role">Administrator</div>
      </div>
    </div>
    <nav class="nav-menu">
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-chart-bar"></i></span> <span>Dashboard</span></a>
      <a class="nav-item active"><span class="nav-icon"><i class="fa fa-users"></i></span> <span>Users</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-book"></i></span> <span>Lectures</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-question"></i></span> <span>Quizzes</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-project-diagram"></i></span> <span>Projects</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-bell"></i></span> <span>Notifications</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-cog"></i></span> <span>Settings</span></a>
      <a class="nav-item" style="color:#e82c37;"><span class="nav-icon"><i class="fa fa-sign-out"></i></span> <span>Logout</span></a>
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="main-content">
    <h1 class="welcome-title">Welcome back, <?php echo $admin_username ?>!</h1>
    <p class="welcome-subtitle">Here’s what's happening with your platform today</p>
    <div class="um-head"><i class="fa fa-users"></i>User Management</div>
  <table class="user-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Password (Reset)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $u): ?>
      <tr>
        <form class="umform" method="post">
          <td><?php echo $u['id']; ?><input type="hidden" name="id" value="<?php echo $u['id']; ?>"></td>
          <td><input type="text" name="name" value="<?php echo htmlspecialchars($u['name']??''); ?>" required></td>
          <td><input type="email" name="email" value="<?php echo htmlspecialchars($u['email']??''); ?>" required></td>
          <td><input type="password" name="password" placeholder="••••••••"></td>
          <td class="user-actions">
            <button name="update_user" value="1" title="Save"><i class="fa fa-save"></i></button>
            <button name="delete_user" value="1" onclick="return confirm('Delete user?');" title="Delete"><i class="fa fa-trash"></i></button>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <!-- Add User -->
  <form method="post" class="add-user-form">
    <h4>Add New User</h4>
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="add_user" value="1"><i class="fa fa-plus"></i> Add User</button>
  </form>
  </main>
      </div>