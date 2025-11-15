<?php
session_start();
require_once '../backend/config.php';
if (!($_SESSION['admin_logged_in'] ?? false)) { header('Location: index.php'); exit; }

$admin_username = ($_SESSION['admin_username']);
// Handle CRUD
if (isset($_POST['add_notification'])) {
    $user_id = trim($_POST['user_id'] ?? null);
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = trim($_POST['type']);
    $link = trim($_POST['link']);
    if ($title && $message) {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type, link) VALUES (?, ?, ?, ?, ?)");
        $null = null;
        $param_user_id = ($user_id === '' ? $null : $user_id);
        $stmt->bind_param(($param_user_id === null ? 'issss' : 'iisss'), $param_user_id, $title, $message, $type, $link);
        if ($param_user_id === null) $stmt->bind_param('ssss', $title, $message, $type, $link);
        $stmt->execute(); $stmt->close();
    }
    header("Location: notifications.php"); exit;
}
if (isset($_POST['update_notification'])) {
    $id = intval($_POST['id']);
    $user_id = trim($_POST['user_id'] ?? null);
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = trim($_POST['type']);
    $link = trim($_POST['link']);
    if ($id && $title && $message) {
        $stmt = $conn->prepare("UPDATE notifications SET user_id=?, title=?, message=?, type=?, link=? WHERE id=?");
        $null = null;
        $param_user_id = ($user_id === '' ? $null : $user_id);
        $stmt->bind_param(($param_user_id === null ? 'ssssi' : 'issssi'), $param_user_id, $title, $message, $type, $link, $id);
        if ($param_user_id === null) $stmt->bind_param('sssssi', $title, $message, $type, $link, $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: notifications.php"); exit;
}
if (isset($_POST['delete_notification'])) {
    $id = intval($_POST['id']);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: notifications.php"); exit;
}

// Users (for targeting)
$users = [];
$usq = $conn->query("SELECT id, name, email FROM users ORDER BY name");
while ($row = $usq->fetch_assoc()) $users[] = $row;

// Fetch all notifications
$notifications = [];
$res = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) $notifications[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications | Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../styles/admindash.css">
<style>
body {background:#f4fff9;font-family:'Poppins',sans-serif;color:#233e27;}
.mainbox {max-width:1100px;margin:44px auto 0;padding:21px 10px;}
.n-head {font-size:2em;font-weight:900;color:#10b859;margin-bottom:13px;display:flex;align-items:center;gap:10px;}
.notification-table {width:100%;border-collapse:collapse;box-shadow:0 3px 16px #10b85918;border-radius:13px;background:#fff;}
.notification-table th,.notification-table td{padding:12px 7px;text-align:left;vertical-align:top;}
.notification-table th {background:#ecfff2;color:#108c43;font-weight:700;}
.notification-table tbody tr:nth-child(even){background:#f6fffa;}
.ntype-info {background:#c9f7ee;color:#108c43;padding:3px 8px;border-radius:8px;font-weight:700;}
.ntype-success {background:#d4fdc1;color:#1b6d1f;padding:3px 8px;border-radius:8px;font-weight:700;}
.ntype-warning {background:#fff6b0;color:#a37a0a;padding:3px 8px;border-radius:8px;font-weight:700;}
.ntype-error {background:#ffd0d6;color:#ad234b;padding:3px 8px;border-radius:8px;font-weight:700;}
.ntype-reminder {background:#e0eaff;color:#14409e;padding:3px 8px;border-radius:8px;font-weight:700;}
.nform input,.nform select,.nform textarea {
  padding:7px 7px;border-radius:7px;border:1px solid #bffdd0;font-size:1em;width:98%;background:#f9fffd;margin-bottom:4px;}
.nform textarea{resize:vertical;min-height:33px;}
.nform button, .add-notification-form button {
  background:#10b859;color:#fff;font-weight:700;border:none;border-radius:7px;padding:6px 11px;cursor:pointer;transition:.14s;}
.nform button:hover, .add-notification-form button:hover {background:#1b7e44;}
.add-notification-form{margin-top:28px;background:#fff;padding:13px 9px 9px 13px;border-radius:13px;border:1.8px solid #10b85946;max-width:670px;}
.add-notification-form h4 {margin-top:0;color:#10b859;}
@media(max-width:890px){.mainbox{padding:5px;}.notification-table th,.notification-table td{padding:6px;font-size:.9em;}}
</style>
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
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-users"></i></span> <span>Users</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-book"></i></span> <span>Lectures</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-question"></i></span> <span>Quizzes</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-project-diagram"></i></span> <span>Projects</span></a>
      <a class="nav-item active"><span class="nav-icon"><i class="fa fa-bell"></i></span> <span>Notifications</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-cog"></i></span> <span>Settings</span></a>
      <a class="nav-item" style="color:#e82c37;"><span class="nav-icon"><i class="fa fa-sign-out"></i></span> <span>Logout</span></a>
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="main-content">
    <h1 class="welcome-title">Welcome back, <?php echo $admin_username ?>!</h1>
    <p class="welcome-subtitle">Here’s what's happening with your platform today</p>
  <div class="n-head"><i class="fa fa-bell"></i>Notifications</div>
  <table class="notification-table">
    <thead>
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Title</th>
      <th>Message</th>
      <th>Type</th>
      <th>Is Read</th>
      <th>Created</th>
      <th>Link</th>
      <th>Actions</th>
    </tr>
    </thead>
    <tbody>
      <?php foreach($notifications as $n): ?>
      <tr>
        <form class="nform" method="post">
          <td><?php echo $n['id']; ?><input type="hidden" name="id" value="<?php echo $n['id']; ?>"></td>
          <td>
            <select name="user_id">
              <option value="">All</option>
              <?php foreach($users as $u): ?>
                <option value="<?php echo $u['id']; ?>" <?php if($u['id']==$n['user_id'])echo'selected';?>><?php echo htmlspecialchars($u['name'].' | '.$u['email']);?></option>
              <?php endforeach;?>
            </select>
          </td>
          <td><input type="text" name="title" value="<?php echo htmlspecialchars($n['title']); ?>" required></td>
          <td><textarea name="message" required><?php echo htmlspecialchars($n['message']);?></textarea></td>
          <td>
            <select name="type">
              <option value="info" <?php if($n['type']=='info')echo'selected';?>>Info</option>
              <option value="success" <?php if($n['type']=='success')echo'selected';?>>Success</option>
              <option value="warning" <?php if($n['type']=='warning')echo'selected';?>>Warning</option>
              <option value="error" <?php if($n['type']=='error')echo'selected';?>>Error</option>
              <option value="reminder" <?php if($n['type']=='reminder')echo'selected';?>>Reminder</option>
            </select>
          </td>
          <td><?php echo $n['is_read'] ? 'Yes' : 'No';?></td>
          <td><?php echo htmlspecialchars($n['created_at']);?></td>
          <td>
            <input type="text" name="link" value="<?php echo htmlspecialchars($n['link']); ?>" placeholder="Optional link">
          </td>
          <td>
            <button name="update_notification" value="1" title="Save"><i class="fa fa-save"></i></button>
            <button name="delete_notification" value="1" onclick="return confirm('Delete notification?');" title="Delete"><i class="fa fa-trash"></i></button>
          </td>
        </form>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <!-- Add Notification -->
  <form method="post" class="add-notification-form">
    <h4>Add Notification</h4>
    <select name="user_id">
      <option value="">All</option>
      <?php foreach($users as $u): ?>
        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name'].' | '.$u['email']);?></option>
      <?php endforeach;?>
    </select>
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="message" placeholder="Message" required></textarea>
    <select name="type">
      <option value="info">Info</option>
      <option value="success">Success</option>
      <option value="warning">Warning</option>
      <option value="error">Error</option>
      <option value="reminder">Reminder</option>
    </select>
    <input type="text" name="link" placeholder="Optional link">
    <button name="add_notification" value="1"><i class="fa fa-plus"></i> Add Notification</button>
  </form>
      </main>
</body>
</html>