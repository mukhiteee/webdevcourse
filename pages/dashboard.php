<?php

session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Quizzes completed
$q = $conn->query("SELECT COUNT(*) AS completed FROM quiz_submissions WHERE user_id=$user_id");
$quiz_completed = $q->fetch_assoc()['completed'] ?? 0;

// Total quizzes
$q = $conn->query("SELECT COUNT(*) AS total FROM course_section WHERE assessment_type='quiz'");
$quiz_total = $q->fetch_assoc()['total'] ?? 0;

// Projects uploaded
$p = $conn->query("SELECT COUNT(*) AS uploaded FROM practical_submissions WHERE user_id=$user_id");
$proj_uploaded = $p->fetch_assoc()['uploaded'] ?? 0;

// Total projects
$p = $conn->query("SELECT COUNT(*) AS total FROM course_section WHERE assessment_type='project'");
$proj_total = $p->fetch_assoc()['total'] ?? 0;

// Average quiz score
$s = $conn->query("SELECT AVG(score) AS avg_score FROM quiz_submissions WHERE user_id=$user_id");
$avg_score = round($s->fetch_assoc()['avg_score'] ?? 0);

$totalLectures = $conn->query("SELECT COUNT(*) FROM lectures")->fetch_row()[0];
$completedQuizzes = $conn->query("SELECT COUNT(DISTINCT lecture_id) FROM quiz_submissions WHERE user_id = $user_id")->fetch_row()[0];
$progress = round(($completedQuizzes / $totalLectures) * 100) ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeWithMukhiteee - Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/dashboard.css">
</head>

<body>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">☰</button>

    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo-section">
                <div class="logo">
                    <h1>CodeWithMukhiteee</h1>
                </div>
            </div>

            <!-- User Profile -->
            <div class="user-profile">
                <div class="user-avatar" id="userAvatar"><?php echo strtoupper(substr($name, 0, 1));?></div>
                <div class="user-info">
                    <div class="user-name" id="userName"><?php echo $name;?></div>
                    <div class="user-role">Premium Learner</div>
                </div>
            </div>
            <nav class="course-nav" id="courseNav">
                <div id="courseModules">
<?php
$section_icons = [
    'Web Fundamentals' => '🌐',
    'HTML5 – The Structure of the Web' => '📄',
    'CSS – The Design of the Web' => '🎨',
    'JavaScript Basics – Logic and Interactivity' => '⚡',
    'Integrating Skills' => '🛠️'
];

$sections_sql = "SELECT id, title FROM sections ORDER BY sequence";
$sections_res = $conn->query($sections_sql);

$moduleIndex = 0;
while ($section = $sections_res->fetch_assoc()) {
    $icon = $section_icons[$section['title']] ?? '📚';

    // Get lectures in this section
    $lectures_sql = "SELECT id, title FROM lectures WHERE section_id = {$section['id']} ORDER BY sequence";
    $lectures_res = $conn->query($lectures_sql);

    echo '<div class="module">';
    echo '  <div class="module-header" id="header">';
    echo '    <span>' . $icon . ' ' . htmlspecialchars($section['title']) . '</span>';
    echo '    <span class="chevron">▶</span>';
    echo '  </div>';
    echo '  <div class="module-sections" id="sections">';
    $sectionIndex = 0;
    while ($lecture = $lectures_res->fetch_assoc()) {
        echo '    <a href="lecture.php?id=' .(int)$lecture['id'].'" style="text-decoration: none; color: white;" class="section-item" data-module="' . $moduleIndex . '" data-section="' . $sectionIndex . '">' .
              htmlspecialchars($lecture['title']) .
              '</a>';
        $sectionIndex++;
    }
    echo '  </div>';
    echo '</div>';

    $moduleIndex++;
}
?>
</div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="welcome-header">
                <h1 class="welcome-title">Welcome back, <span id="welcomeName"><?php echo $name;?></span>! 🚀</h1>
                <p class="welcome-subtitle">Track your learning journey and celebrate your achievements</p>
            </div>
  
    <div class="right-panel" style="
    display: flex;
    align-items: center;
    gap: 14px;">
        <form action="logout.php" method="POST"><button class="logout-btn" style="
    background: #e9ecef;
    border: none;
    color: #353547;
    padding: 7px 16px;
    border-radius: 14px;
    cursor:pointer;
    font-weight: 500;">Logout</button></form>
    </div>

<!-- Quick Links Card Section (after stats) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="quick-links-panel">
  <div class="quick-link-card" onclick="window.open('notifications.php')">
    <div class="ql-icon"><i class="fas fa-bullhorn"></i></div>
    <div class="ql-label">Notifications</div>
  </div>
  <div class="quick-link-card" onclick="window.open('https://yourdocslink.com','_blank')">
    <div class="ql-icon"><i class="fas fa-book-open"></i></div>
    <div class="ql-label">Official Docs</div>
  </div>
  <div class="quick-link-card" onclick="window.open('https://yournewuserguide.com','_blank')">
    <div class="ql-icon"><i class="fas fa-compass"></i></div>
    <div class="ql-label">New User Guide</div>
  </div>
  <div class="quick-link-card" onclick="window.open('https://yourcommunityinvite.com','_blank')">
    <div class="ql-icon"><i class="fas fa-users"></i></div>
    <div class="ql-label">Join Community</div>
  </div>
  <div class="quick-link-card" onclick="window.open('https://yourresourcepage.com','_blank')">
    <div class="ql-icon"><i class="fas fa-folder-open"></i></div>
    <div class="ql-label">Resource Library</div>
  </div>
  <div class="quick-link-card" onclick="window.open('https://yourleaderboard.com','_blank')">
    <div class="ql-icon"><i class="fas fa-trophy"></i></div>
    <div class="ql-label">Leaderboard</div>
  </div>
</div>

<style>
.quick-links-panel {
  display: flex; flex-wrap: wrap; gap: 19px 12px;
  margin: 22px 0 6px; justify-content: flex-start;
}
.quick-link-card {
  background: #f4fff8;
  border-radius: 15px;
  box-shadow: 0 1px 9px #b0ffc822;
  padding: 21px 12px 13px 12px;
  min-width: 108px; flex: 1 0 42%; max-width: 178px;
  display: flex; flex-direction: column; align-items: center; cursor: pointer;
  transition: box-shadow .18s, background .15s;
  border: 2.5px solid #10b8591c;
  text-align: center;
}
.quick-link-card:hover {
  background: #e4fff2;
  box-shadow: 0 2px 21px #18ba7240;
  transform: translateY(-2px) scale(1.04);
}
.ql-icon {
  font-size: 2.23em; color: #10b859; margin-bottom: 8px;
}
.ql-label {
  font-size: 1em; color: #07703e; font-weight: 600; word-break: break-word; letter-spacing:.02em;
}
@media (max-width:600px) {
  .quick-link-card { min-width: 46vw; max-width: 96vw; }
  .quick-links-panel { gap: 12px 2vw; }
}
</style>
<br><br>
            <!-- Analytics Panel -->
            <div class="analytics-panel">
                <div class="progress-section">
                    <div class="progress-header">
                        <h3 class="progress-label">Course Completion Progress</h3>
                        <span class="progress-percentage" id="overallProgress"><?php echo $progress ?>%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar" style="width: <?php echo $progress ?>%"></div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
    <div class="stat-icon">📝</div>
    <div class="stat-value"><?php echo "$quiz_completed"; ?></div>
    <div class="stat-label">Quizzes Completed</div>
</div>
<div class="stat-card">
    <div class="stat-icon">🚀</div>
    <div class="stat-value"><?php echo "$proj_uploaded"; ?></div>
    <div class="stat-label">Projects Uploaded</div>
</div>
<div class="stat-card">
    <div class="stat-icon">⭐</div>
    <div class="stat-value"><?php echo $avg_score . "%"; ?></div>
    <div class="stat-label">Average Score</div>
</div>
                </div>

                <!-- Full Results Card with Download as PDF & Image
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="crazy-result-card" id="resultSummaryCard">
  <div class="crc-header">
    <span class="crc-brand"><i class="fas fa-bolt"></i> GitCircle <span>&times;</span> CodeWithMukhiteee</span>
    <span class="crc-badge">Ultimate Result Sheet</span>
  </div>
  <div class="crc-user">
    <span class="crc-user-avatar"><?= strtoupper(substr($_SESSION['name'] ?? 'M',0,1)) ?></span>
    <div>
      <div class="crc-user-name"><?= htmlspecialchars($_SESSION['name'] ?? 'Student') ?></div>
      <div class="crc-stats-time"><i class="fas fa-clock"></i> <?= date('M j, Y h:ia') ?></div>
    </div>
    <div>
      <button class="crc-download" style="margin-bottom:6px;" onclick="downloadCardAsImage()">Image <i class="fas fa-image"></i></button>
      <button class="crc-download" onclick="downloadCardAsPDF()">PDF <i class="fas fa-file-pdf"></i></button>
    </div>
  </div>
  <div class="crc-table-wrap">
    <table class="crc-table">
      <thead>
        <tr>
          <th>Lecture</th>
          <th>Quiz Score</th>
          <th>Quiz %</th>
          <th>Practical</th>
          <th>Score</th>
          <th>Feedback</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $lectures = $conn->query("SELECT id, title FROM lectures ORDER BY id");
        while ($lec = $lectures->fetch_assoc()):
          $quiz = $conn->query("SELECT score, submission_time FROM quiz_submissions WHERE user_id=$user_id AND lecture_id={$lec['id']} ORDER BY id DESC LIMIT 1")->fetch_assoc();
          $prac = $conn->query("SELECT scored, score, feedback, submitted_at FROM practical_submissions WHERE user_id=$user_id AND lecture_id={$lec['id']} ORDER BY id DESC LIMIT 1")->fetch_assoc();
          ?>
          <tr>
            <td><b><?= htmlspecialchars($lec['title']) ?></b></td>
            <td>
              <?php if($quiz): ?>
                <span class="crc-quizscore"><?= $quiz['score'] ?>%</span>
              <?php else: ?>
                <span class="crc-badgestatus">---</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($quiz && $quiz['score'] >= 70): ?>
                <span class="crc-pass">✅ Pass</span>
              <?php elseif($quiz): ?>
                <span class="crc-fail">❌ Below mark</span>
              <?php else: ?>
                <span class="crc-badgestatus">N/A</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($prac): ?>
                <?= $prac['scored'] ? '✔️ Graded' : '⏳ Pending' ?>
              <?php else: ?>
                <span class="crc-badgestatus">---</span>
              <?php endif; ?>
            </td>
            <td>
              <?= ($prac && $prac['scored']) ? ("<span class='crc-pracscore'>{$prac['score']}/100</span>") : '<span class="crc-badgestatus">—</span>' ?>
            </td>
            <td>
              <?= (!empty($prac['feedback'])) ? nl2br(htmlspecialchars($prac['feedback'])) : '<i class="crc-muted">-</i>' ?>
            </td>
            <td>
              <?php
                echo $quiz ? date('M j H:i', strtotime($quiz['submission_time'])) : '';
                if($prac && $prac['submitted_at']) echo "<br><span class='crc-muted'>" . date('M j H:i', strtotime($prac['submitted_at'])) . "</span>";
              ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div class="crc-share">
    <span>Share your result 💥</span>
    <button onclick="shareResults()" class="crc-share-btn"><i class="fas fa-share-alt"></i> Share</button>
  </div>
</div> -->
<!-- 
<style>
.crazy-result-card {
  background: linear-gradient(100deg,#fff,#e7fff3 78%,#cbffe3 100%);
  border-radius: 22px;
  box-shadow: 0 8px 54px #1effa026, 0 1px 9px #0fae5742;
  padding: 24px 12px 18px 12px;
  margin: 27px 0 22px;
  font-family:'Poppins','Segoe UI',sans-serif;
  overflow-x: auto;
  border: 3px solid #07e05a33;
}
.crc-header {
  display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;
}
.crc-brand {
  font-size:1.16em;font-weight:800;color:#137d4a;letter-spacing:1.7px;
  text-transform:uppercase;display:inline-flex;gap:6px;align-items:center;
}
.crc-badge {
  background:linear-gradient(90deg,#10b859,#34ffb1);
  color:#fff;padding:7px 17px 5px 17px;border-radius:13px;font-size: 1em;
  font-weight:800;box-shadow:0 1px 9px #44ffb060;letter-spacing:.05em;
}
.crc-user {
  display:flex;align-items:center;gap:13px;margin:10px 0 19px 0;
}
.crc-user-avatar {
  background:linear-gradient(130deg,#16ff7c,#05703e 80%);
  width:42px;height:42px;border-radius:50%;color:#fff;
  font-size:1.35em;display:flex;align-items:center;justify-content:center;font-weight:900;
  box-shadow:0 1px 5px #0fae5737;
}
.crc-user-name {font-weight:700;color:#073733;}
.crc-stats-time {font-size:.93em;color:#4eb37a;}
.crc-download {
  background:#09e716;
  color:#fff;font-weight:700;border-radius:11px;padding:7px 15px;font-size:1.02em;border:none;box-shadow:0 1px 9px #cbffe3;cursor:pointer;
  transition:.14s;margin-left:6px;}
.crc-download:hover {background:#0fae57;}
.crc-table-wrap {overflow-x:auto;}
.crc-table { width:100%;margin-top:15px;border-collapse:collapse; font-size:1.02em;box-shadow:0 0 0 1px #16e07322; border-radius:9px;overflow:hidden;min-width:640px;}
.crc-table th,.crc-table td {padding:12px 8px;border-bottom:1.5px solid #d9ffed;text-align:left;}
.crc-table th {background:#e7fff3;color:#087741;font-weight:800;letter-spacing:1.1px;}
.crc-table tr:last-child td {border-bottom:none;}
.crc-pracscore,.crc-quizscore {color:#10b859;font-weight:900;}
.crc-pass {color:#0fae57;font-weight:600;}
.crc-fail {color:#ce3b1c;}
.crc-badgestatus {opacity:.5;color:#8abb91;}
.crc-muted {opacity:.65;font-size:.98em;}
.crc-share {display:flex;align-items:center;margin-top:16px;gap:15px;}
.crc-share-btn {
  background:linear-gradient(90deg,#10b859 90%,#03d89e);
  color:#fff;font-weight:800;border-radius:10px;padding:9px 23px 9px 15px;font-size:1.04em;border:none;box-shadow:0 1px 9px #4cffc7;cursor:pointer;transition:.11s;display:inline-flex;gap:7px; align-items:center;}
.crc-share-btn:hover {background:#0fd978;}
@media (max-width:750px){.crc-table{min-width:504px;}}
@media (max-width:570px){.crc-table{min-width:380px;}}
</style>

<script>
function downloadCardAsImage(){
  const card = document.getElementById('resultSummaryCard');
  html2canvas(card).then(function(canvas){
    let link = document.createElement('a');
    link.download = 'GitCircle_Mukhiteee_Results.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
  });
}
function downloadCardAsPDF() {
  const card = document.getElementById('resultSummaryCard');
  html2canvas(card).then(function(canvas) {
    const { jsPDF } = window.jspdf;
    const imgData = canvas.toDataURL('image/png');
    const pdf = new jsPDF({
      orientation: 'landscape',
      unit: 'px',
      format: [canvas.width, canvas.height]
    });
    pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
    pdf.save('GitCircle_Mukhiteee_Results.pdf');
  });
}
function shareResults(){
  if (navigator.share) {
    navigator.share({
      title: 'My CodeWithMukhiteee Results',
      text: 'Check out my coding course progress!',
      url: window.location.href
    });
  } else {
    alert('Share is not supported on your device. Try downloading as PDF/image or screenshot!');
  }
}
</script> -->



<script src="../scripts/dashboard.js"></script>
<script>
function toggleAccordion(btn) {
    btn.classList.toggle('active');
    var panel = btn.nextElementSibling;
    if (panel.style.display === "block") {
        panel.style.display = "none";
    } else {
        panel.style.display = "block";
    }
}


        // Update Analytics with Animation
        function updateAnalytics() {
            const overallProgress = <?php echo $progress ?>
            
                console.log(overallProgress);

            // Animate progress bar
            setTimeout(() => {
                document.getElementById('progressBar').style.width = overallProgress + '%';
                animateNumber('overallProgress', 0, overallProgress, 1500, '%');
            }, 300);
        }

</script>