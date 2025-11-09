<?php
session_start();
require_once '../backend/config.php';

$lecture_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Fetch lecture & section
$lecture_sql = "SELECT l.id, l.title as lecture_title, l.description, l.has_practical, s.title as section_title
    FROM lectures l JOIN sections s ON l.section_id = s.id WHERE l.id = $lecture_id";
$lecture1 = $conn->query($lecture_sql)->fetch_assoc();

// Fetch resources
$res = $conn->query("SELECT * FROM resources WHERE lecture_id = $lecture_id");

// Fetch quiz questions
$qz = $conn->query("SELECT * FROM quiz_questions WHERE lecture_id = $lecture_id");

// Fetch practical submission (if any)
$prac = $conn->query("SELECT * FROM practical_submissions WHERE user_id=$user_id AND lecture_id=$lecture_id")->fetch_assoc();

// Progress calculation (example)
$progress = 65; // Replace with real calculation

// User info (update to your session/user modeling)
$name = $_SESSION['name'] ?? 'M';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($lecture1['lecture_title']) ?> | CodeWithMukhiteee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/lecture.css">
    <link rel="stylesheet" href="../styles/dashboard.css">
</head>
<body>
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

        <div class="main-content" style="background-color: #eee;">
<div class="top-header">
    <div class="breadcrumbs">
        <a href="dashboard.php">Dashboard</a> › <?= htmlspecialchars($lecture1['section_title']) ?> › <?= htmlspecialchars($lecture1['lecture_title']) ?>
    </div>
    <div class="right-panel">
        <div class="user-avatar"><?= strtoupper(substr($name,0,1)) ?></div>
        <form action="logout.php" method="POST"><button class="logout-btn">Logout</button></form>
    </div>
</div>

<div class="lecture-card">
    <!-- Progress Card -->
    <div class="progress-card">
        <div style="color:#10b859;font-weight:bold;"><?= htmlspecialchars($lecture1['section_title']) ?></div>
        <div class="lecture-title"><?= htmlspecialchars($lecture1['lecture_title']) ?></div>
        <div class="progress-bar-container"><div class="progress-bar" style="width:<?= $progress ?>%;"></div></div>
        <span class="completion-percent" style="color:#10b859;"><?= $progress ?>% Complete</span>
    </div>

    <!-- Overview -->
    <div class="overview-card">
        <h3>Lecture Overview</h3>
        <p><?= nl2br(htmlspecialchars($lecture1['description'])) ?></p>
    </div>

    <!-- Resource Cards -->
    <div class="overview-card">
        <h4 style="font-size:18px;margin:14px 0 7px; color: #00953c">Learning Resources</h4>
        <?php while ($r = $res->fetch_assoc()): ?>
            <div class="cw-green-card" style="background:<?= $r['type']=='pdf' ? '#effff9' : ($r['type']=='video' ? '#f5f8ff' : '#fafdfe') ?>;">
                <div class="cw-card-icon"><i class="<?= htmlspecialchars($r['icon']) ?>"></i></div>
                <div class="cw-card-content">
                    <div class="cw-card-title"><?= htmlspecialchars($r['title']) ?></div>
                    <div class="cw-card-desc"><?= htmlspecialchars($r['description']) ?></div>
                    <a href="<?= htmlspecialchars($r['link']) ?>" class="cw-download-btn" target="_blank"><?= $r['action'] ?></a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Practical Assignment -->
    <?php if ($lecture1['has_practical']): ?>
    <div class="practical-section">
        <h4>Practical Assignment</h4>
        <p>Create a responsive layout using Flexbox or Grid. Push your code to GitHub and provide your link below.</p>
        <?php if (!$prac || !$prac['scored']): ?>
        <form action="submit_practical.php" method="post">
            <input type="url" name="github_link" placeholder="Paste your GitHub repo link" value="<?= $prac && $prac['github_link'] ? htmlspecialchars($prac['github_link']) : '' ?>" required class="github-input"/>
            <input type="hidden" name="lecture_id" value="<?= $lecture_id ?>">
            <button type="submit" class="cw-download-btn" style="margin-top:7px;">Submit Assignment</button>
        </form>
        <?php endif; ?>
        <?php if ($prac): ?>
        <div class="submission-card">
            <b>Recent Submission:</b>
            <a href="<?= htmlspecialchars($prac['github_link']) ?>" class="submission-link"><?= htmlspecialchars($prac['github_link']) ?></a>
            <?= $prac['scored'] ? "<div>Score: {$prac['score']}</div>" : '<div>Awaiting grading</div>' ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Quiz Section -->
    <?php
// Fetch all questions for this lecture
$q_sql = "SELECT * FROM quiz_questions WHERE lecture_id = $lecture_id";
$result = $conn->query($q_sql);
$qno = 1;

while ($q = $result->fetch_assoc()) {
  echo '<div class="quiz-question">';
  echo "<b>Question $qno:</b> ".htmlspecialchars($q['question'])."</div>";
  foreach (['A','B','C','D'] as $key) {
    $col = 'option'.strtolower($key);
    if (!empty($q[$col])) {
      echo "<label><input type='radio' name='ans[{$q['id']}]' value='$key'> ".htmlspecialchars($q[$col])."</label><br>";
    }
  }
  $qno++;
}
?>
</div>
        </div>
</body>
</html>