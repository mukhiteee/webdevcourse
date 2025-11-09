<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
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
$p = $conn->query("SELECT COUNT(*) AS uploaded FROM project_submissions WHERE user_id=$user_id");
$proj_uploaded = $p->fetch_assoc()['uploaded'] ?? 0;

// Total projects
$p = $conn->query("SELECT COUNT(*) AS total FROM course_section WHERE assessment_type='project'");
$proj_total = $p->fetch_assoc()['total'] ?? 0;

// Average quiz score
$s = $conn->query("SELECT AVG(score) AS avg_score FROM quiz_submissions WHERE user_id=$user_id");
$avg_score = round($s->fetch_assoc()['avg_score'] ?? 0);

// Progress (simplified)
$stmt = $conn->query("SELECT COUNT(*) AS total FROM course_section");
$total_sections = $stmt->fetch_assoc()['total'];
$stmt = $conn->query("SELECT COUNT(DISTINCT section_id) AS done FROM (
    SELECT section_id FROM quiz_submissions WHERE user_id=$user_id
    UNION
    SELECT section_id FROM project_submissions WHERE user_id=$user_id
) AS all_done");
$done_sections = $stmt->fetch_assoc()['done'];
$progress = $total_sections > 0 ? intval(($done_sections/$total_sections)*100) : 0;
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
    echo '  <div class="module-header">';
    echo '    <span>' . $icon . ' ' . htmlspecialchars($section['title']) . '</span>';
    echo '    <span class="module-icon">▶</span>';
    echo '  </div>';
    echo '  <div class="module-sections">';
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

            <!-- Analytics Panel -->
            <div class="analytics-panel">
                <div class="progress-section">
                    <div class="progress-header">
                        <h3 class="progress-label">Course Completion Progress</h3>
                        <span class="progress-percentage" id="overallProgress">0%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
    <div class="stat-icon">📝</div>
    <div class="stat-value"><?php echo "$quiz_completed/$quiz_total"; ?></div>
    <div class="stat-label">Quizzes Completed</div>
</div>
<div class="stat-card">
    <div class="stat-icon">🚀</div>
    <div class="stat-value"><?php echo "$proj_uploaded/$proj_total"; ?></div>
    <div class="stat-label">Projects Uploaded</div>
</div>
<div class="stat-card">
    <div class="stat-icon">⭐</div>
    <div class="stat-value"><?php echo $avg_score . "%"; ?></div>
    <div class="stat-label">Average Score</div>
</div>
<span class="progress-percentage"><?php echo $progress; ?>%</span>
<div class="progress-bar" style="width:<?php echo $progress; ?>%"></div>




<script src="../scripts/main.js"></script>
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
</script>