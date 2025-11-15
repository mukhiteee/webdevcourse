<?php
session_start();
require_once '../backend/config.php';
if (!($_SESSION['admin_logged_in'] ?? false)) { header('Location: index.php'); exit; }

$admin_username = ($_SESSION['admin_username']);
// UPDATE submission (score, feedback)
if (isset($_POST['update_project'])) {
    $id = intval($_POST['id']);
    $scored = isset($_POST['scored']) ? 1 : 0;
    $score = ($_POST['score'] !== '') ? intval($_POST['score']) : null;
    $feedback = trim($_POST['feedback'] ?? '');
    if ($id) {
        $stmt = $conn->prepare("UPDATE practical_submissions SET scored=?, score=?, feedback=? WHERE id=?");
        $stmt->bind_param('iisi', $scored, $score, $feedback, $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: projects.php"); exit;
}
// DELETE submission
if (isset($_POST['delete_project'])) {
    $id = intval($_POST['id']);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM practical_submissions WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: projects.php"); exit;
}

// Get practical subs, plus user & lecture info
$result = $conn->query("SELECT ps.*, u.name AS username, l.title AS lecturetitle
    FROM practical_submissions ps
    LEFT JOIN users u ON ps.user_id = u.id
    LEFT JOIN lectures l ON ps.lecture_id = l.id
    ORDER BY ps.submitted_at DESC
    ");
$subs = [];
while ($row = $result->fetch_assoc()) $subs[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Project Submissions | Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../styles/admindash.css">
<style>
body {background:#f4fff9;font-family:'Poppins',sans-serif;color:#233e27;}
.mainbox {max-width:1150px;margin:44px auto 0;padding:23px 13px;}
.p-head {font-size:2em;font-weight:900;color:#10b859;margin-bottom:13px;display:flex;align-items:center;gap:10px;}
.project-table {width:100%;border-collapse:collapse;box-shadow:0 3px 19px #10b85918;border-radius:13px;overflow:hidden;background:#fff;}
.project-table th, .project-table td {padding:12px 8px;text-align:left;vertical-align:top;}
.project-table th {background:#ecfff2;color:#108c43;font-weight:700;}
.project-table tbody tr:nth-child(even){background:#f6fffa;}
.project-table td {font-size:.98em;}
.pform textarea {padding:7px 8px;border-radius:7px;border:1px solid #bffdd0;font-size:1em;width:100%;background:#f9fffd;resize:vertical;}
.pform input[type=number] {width:55px;}
.pform input, .pform textarea {margin-bottom:3px;}
.pform button {
  background:#10b859;color:#fff;font-weight:700;border:none;border-radius:7px;padding:6px 11px;cursor:pointer;transition:.14s;}
.pform button:hover {background:#1b7e44;}
@media(max-width:900px){.mainbox{padding:5px;}.project-table th,.project-table td{padding:6px;font-size:.9em;}}
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
      <a class="nav-item active"><span class="nav-icon"><i class="fa fa-project-diagram"></i></span> <span>Projects</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-bell"></i></span> <span>Notifications</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-cog"></i></span> <span>Settings</span></a>
      <a class="nav-item" style="color:#e82c37;"><span class="nav-icon"><i class="fa fa-sign-out"></i></span> <span>Logout</span></a>
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="main-content">
    <h1 class="welcome-title">Welcome back, <?php echo $admin_username ?>!</h1>
    <p class="welcome-subtitle">Here’s what's happening with your platform today</p>
<div class="mainbox">
  <div class="p-head"><i class="fa fa-file-code"></i>Project Submissions</div>
  <table class="project-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Lecture</th>
        <th>GitHub Link</th>
        <th>Submitted At</th>
        <th>Scored?</th>
        <th>Score</th>
        <th>Feedback</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($subs as $s): ?>
      <tr>
        <form class="pform" method="post">
          <td><?php echo $s['id']; ?><input type="hidden" name="id" value="<?php echo $s['id']; ?>"></td>
          <td><?php echo htmlspecialchars($s['username']??''); ?></td>
          <td><?php echo htmlspecialchars($s['lecturetitle']??''); ?></td>
          <td>
            <a href="<?php echo htmlspecialchars($s['github_link']); ?>" target="_blank">
              <?php echo htmlspecialchars($s['github_link']); ?>
            </a>
          </td>
          <td><?php echo htmlspecialchars($s['submitted_at']); ?></td>
          <td><input type="checkbox" name="scored" <?php if($s['scored']) echo 'checked'; ?>></td>
          <td><input type="number" name="score" value="<?php echo htmlspecialchars($s['score']); ?>"></td>
          <td><textarea name="feedback"><?php echo htmlspecialchars($s['feedback']); ?></textarea></td>
          <td>
            <button name="update_project" value="1" title="Save"><i class="fa fa-save"></i></button>
            <button name="delete_project" value="1" onclick="return confirm('Delete this submission?');" title="Delete"><i class="fa fa-trash"></i></button>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
      </main>
</div>
</body>
</html>