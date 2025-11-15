<?php
session_start();
require_once '../backend/config.php';
if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: index.php');
    exit;
}
$admin_username = ($_SESSION['admin_username']);
// Handle CRUD
if (isset($_POST['add_lecture'])) {
    $section_id = intval($_POST['section_id']);
    $title = trim($_POST['title'] ?? '');
    $description = $_POST['description'] ?? '';
    $sequence = intval($_POST['sequence']);
    $has_quiz = isset($_POST['has_quiz']) ? 1 : 0;
    $has_practical = isset($_POST['has_practical']) ? 1 : 0;
    if ($title && $section_id && $sequence) {
        $stmt = $conn->prepare("INSERT INTO lectures (section_id, title, description, sequence, has_quiz, has_practical)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issiii', $section_id, $title, $description, $sequence, $has_quiz, $has_practical);
        $stmt->execute(); $stmt->close();
    }
    header("Location: lectures.php"); exit;
}
if (isset($_POST['update_lecture'])) {
    $id = intval($_POST['id']);
    $section_id = intval($_POST['section_id']);
    $title = trim($_POST['title'] ?? '');
    $description = $_POST['description'] ?? '';
    $sequence = intval($_POST['sequence']);
    $has_quiz = isset($_POST['has_quiz']) ? 1 : 0;
    $has_practical = isset($_POST['has_practical']) ? 1 : 0;
    if ($id && $title && $section_id && $sequence) {
        $stmt = $conn->prepare("UPDATE lectures SET section_id=?, title=?, description=?, sequence=?, has_quiz=?, has_practical=? WHERE id=?");
        $stmt->bind_param('issiiii', $section_id, $title, $description, $sequence, $has_quiz, $has_practical, $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: lectures.php"); exit;
}
if (isset($_POST['delete_lecture'])) {
    $id = intval($_POST['id']);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM lectures WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: lectures.php"); exit;
}

// Get all lectures
$lectures = [];
$res = $conn->query("SELECT * FROM lectures ORDER BY section_id, sequence");
while ($row = $res->fetch_assoc()) $lectures[] = $row;

// Get sections for select options
$sections = [];
$res2 = $conn->query("SELECT id, title FROM sections ORDER BY sequence");
while ($row = $res2->fetch_assoc()) $sections[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Lecture Management | Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../styles/admindash.css">
<style>
body {background:#f4fff9;font-family:'Poppins',sans-serif;color:#233e27;}
.mainbox {max-width:1100px;margin:45px auto 0;padding:20px 12px;}
.l-head {font-size:2em;font-weight:900;color:#10b859;margin-bottom:11px;display:flex;align-items:center;gap:11px;}
.lecture-table {width:100%;border-collapse:collapse;box-shadow:0 3px 18px #10b85918;border-radius:13px;overflow:hidden;background:#fff;}
.lecture-table th, .lecture-table td {padding:14px 10px;text-align:left;}
.lecture-table th {background:#ecfff2;color:#108c43;font-weight:700;}
.lecture-table tbody tr:nth-child(even){background:#f6fffa;}
.lecture-table td {font-size:1.01em;}
.lform input, .lform select, .lform textarea {
  padding:7px;border-radius:8px;border:1.3px solid #bffdd0;font-size:1em;width:100%;background:#f9fffd;
  margin-bottom:5px;
}
textarea {font-family:'Poppins',sans-serif;resize:vertical;}
.lform button, .add-lecture-form button {
  background:#10b859;color:#fff;font-weight:700;border:none;border-radius:7px;padding:8px 14px;cursor:pointer;transition:.14s;}
.lform button:hover, .add-lecture-form button:hover {background:#1b7e44;}
.add-lecture-form {margin-top:29px;background:#fff;padding:17px 16px;border-radius:13px;border:1.8px solid #10b85946;max-width:780px;}
.add-lecture-form h4 {margin-top:0;color:#10b859;}
@media(max-width:700px){
  .mainbox {padding:5px;}
  .add-lecture-form {max-width:100%;}
  .lecture-table th,.lecture-table td {padding:6px;font-size:.97em;}
}
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
      <a class="nav-item active"><span class="nav-icon"><i class="fa fa-book"></i></span> <span>Lectures</span></a>
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
  <div class="l-head"><i class="fa fa-book"></i>Lecture Management</div>
  <table class="lecture-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Section</th>
        <th>Title</th>
        <th>Description</th>
        <th>Sequence</th>
        <th>Quiz?</th>
        <th>Practical?</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($lectures as $l): ?>
      <tr>
        <form class="lform" method="post">
          <td><?php echo $l['id']; ?><input type="hidden" name="id" value="<?php echo $l['id']; ?>"></td>
          <td>
            <select name="section_id">
              <?php foreach($sections as $s): ?>
                <option value="<?php echo $s['id']; ?>"<?php if($s['id'] == $l['section_id']) echo ' selected'; ?>><?php echo htmlspecialchars($s['title']); ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td><input type="text" name="title" value="<?php echo htmlspecialchars($l['title']); ?>" required></td>
          <td><textarea name="description" rows="2"><?php echo htmlspecialchars($l['description']??''); ?></textarea></td>
          <td><input type="number" name="sequence" value="<?php echo $l['sequence']; ?>" min="1" required style="width:60px"></td>
          <td><input type="checkbox" name="has_quiz" <?php if($l['has_quiz']) echo 'checked'; ?>></td>
          <td><input type="checkbox" name="has_practical" <?php if($l['has_practical']) echo 'checked'; ?>></td>
          <td>
            <button name="update_lecture" value="1" title="Save"><i class="fa fa-save"></i></button>
            <button name="delete_lecture" value="1" onclick="return confirm('Delete lecture?');" title="Delete"><i class="fa fa-trash"></i></button>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <!-- Add Lecture -->
  <form method="post" class="add-lecture-form">
    <h4>Add New Lecture</h4>
    <select name="section_id" required>
      <option value="">--Section--</option>
      <?php foreach($sections as $s): ?>
        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['title']); ?></option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="title" placeholder="Lecture Title" required>
    <textarea name="description" placeholder="Description" rows="2"></textarea>
    <input type="number" name="sequence" min="1" placeholder="Sequence" required>
    <label><input type="checkbox" name="has_quiz" checked> Has Quiz?</label>
    <label><input type="checkbox" name="has_practical"> Has Practical?</label>
    <br>
    <button name="add_lecture" value="1"><i class="fa fa-plus"></i> Add Lecture</button>
  </form>
</div>
</body>
</html>