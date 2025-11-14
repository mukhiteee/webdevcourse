<?php
session_start();
require_once '../backend/config.php';
if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: index.php');
    exit;
}

// TOTALS
$userCount = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?? 0;
$lectureCount = $conn->query("SELECT COUNT(*) FROM lectures")->fetch_row()[0] ?? 0;
$quizSubCount = $conn->query("SELECT COUNT(*) FROM quiz_submissions")->fetch_row()[0] ?? 0;
$projCount = $conn->query("SELECT COUNT(*) FROM practical_submissions")->fetch_row()[0] ?? 0;

// Recent activity (combine latest users, quizzes, practicals)
$activity_sql = "SELECT name AS act_name, created_at AS act_time, 'user' AS type FROM users
                 UNION ALL
                 SELECT NULL, submission_time, 'quiz' FROM quiz_submissions
                 UNION ALL
                 SELECT NULL, submitted_at, 'project' FROM practical_submissions
                 ORDER BY act_time DESC LIMIT 5";
$activity = [];
$q = $conn->query($activity_sql);
while ($row = $q->fetch_assoc()) $activity[] = $row;

// Student Table
$student_sql = "SELECT u.name, u.email,
    IFNULL(ROUND( ( (SELECT COUNT(DISTINCT qs.lecture_id) FROM quiz_submissions qs WHERE qs.user_id = u.id) / (SELECT COUNT(*) FROM lectures) ) * 100 ), 0) AS progress,
    (SELECT COUNT(*) FROM quiz_submissions qs WHERE qs.user_id = u.id) AS quizzes_completed,
    (SELECT COUNT(*) FROM lectures) AS total_quizzes,
    (SELECT COUNT(*) FROM practical_submissions ps WHERE ps.user_id = u.id) AS assignments_completed,
    (SELECT COUNT(*) FROM lectures WHERE has_practical=1) AS total_assignments,
    'active' AS status
    FROM users u ORDER BY u.id DESC LIMIT 5";
$students = [];
$r = $conn->query($student_sql);
while ($row = $r->fetch_assoc()) $students[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../styles/admindash.css">
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
      <a class="nav-item active"><span class="nav-icon"><i class="fa fa-chart-bar"></i></span> <span>Dashboard</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-users"></i></span> <span>Students</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-book"></i></span> <span>Courses</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-question"></i></span> <span>Quizzes</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-project-diagram"></i></span> <span>Projects</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-bell"></i></span> <span>Notifications</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-cog"></i></span> <span>Settings</span></a>
      <a class="nav-item" style="color:#e82c37;"><span class="nav-icon"><i class="fa fa-sign-out"></i></span> <span>Logout</span></a>
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="main-content">
    <h1 class="welcome-title">Welcome back, Admin!</h1>
    <p class="welcome-subtitle">Here’s what's happening with your platform today</p>
    <!-- Stats -->
    
    <div class="stats-overview">
      <div class="stat-card">
        <div class="stat-icon"><i class="fa fa-users"></i></div>
        <span class="stat-label">Total Students</span>
        <span class="stat-value"><?= $userCount ?></span> <!-- Total Students -->
        <span class="stat-change">+12 this week</span>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="fa fa-book"></i></div>
        <span class="stat-label">Total Lectures</span>
        <span class="stat-value"><?= $lectureCount ?></span> <!-- Total Lectures -->
        <span class="stat-change">+5 sections</span>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="fa fa-star"></i></div>
        <span class="stat-label">Quiz Submissions</span>
        <span class="stat-value"><?= $quizSubCount ?></span> <!-- Quiz Submissions -->
        <span class="stat-change">+28 today</span>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="fa fa-file-code"></i></div>
        <span class="stat-label">Practical Submissions</span>
        <span class="stat-value"><?= $projCount ?></span> <!-- Project Submissions -->
        <span class="stat-change">+8 pending review</span>
      </div>
    </div>
    <!-- Content Panels -->
    <div class="content-panels">
      <div class="panel">
        <div class="panel-title"><i class="fa fa-bolt"></i> Recent Activity</div>
        <?php foreach ($activity as $a): ?>
  <div class="activity-item">
    <div class="activity-time"><?= date('M j, H:i', strtotime($a['act_time'])) ?></div>
    <div class="activity-text">
      <?php
        if ($a['type'] == 'user' && $a['actor']) echo "New registration: <b>" . htmlspecialchars($a['actor']) . "</b>";
        elseif ($a['type'] == 'quiz') echo "Quiz submitted";
        elseif ($a['type'] == 'practical') echo "Practical submitted";
      ?>
    </div>
  </div>
<?php endforeach; ?>
      </div>
      <div class="panel">
        <div class="panel-title"><i class="fa fa-bolt"></i> Quick Actions</div>
        <div class="quick-actions">
          <button class="action-btn"><i class="fa fa-book"></i> Add Lecture</button>
          <button class="action-btn"><i class="fa fa-star"></i> Create Quiz</button>
          <button class="action-btn"><i class="fa fa-bell"></i> Send Notification</button>
          <button class="action-btn"><i class="fa fa-chart-bar"></i> View Reports</button>
        </div>
      </div>
    </div>
    <!-- Performance Table -->
    <div class="data-table">
      <div class="table-header">
        <div class="table-title">Student Performance Overview</div>
      </div>
      <table>
        <thead>
          <tr>
            <th>Student Name</th>
            <th>Email</th>
            <th>Progress</th>
            <th>Quizzes</th>
            <th>Projects</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($students as $s): ?>
<tr>
  <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
  <td><?= htmlspecialchars($s['email']) ?></td>
  <td><?= intval($s['progress']) ?>%</td>
  <td><?= intval($s['quizzes_completed']) . '/' . intval($s['total_quizzes']) ?></td>
  <td><?= intval($s['assignments_completed']) . '/' . intval($s['total_assignments']) ?></td>
  <td><span class="badge active">Active</span></td>
</tr>
<?php endforeach; ?>
</tbody>
      </table>
    </div>