<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit();
}

$lecture_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header ('Location: dashboard.php');
}

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

    <!-- Quiz Section -->
<?php
$user_id = $_SESSION['user_id'];
$lecture_id = (int)$_GET['id'];

// Total questions in this quiz
$q_count_res = $conn->query("SELECT COUNT(*) AS total FROM quiz_questions WHERE lecture_id = $lecture_id");
$q_count = $q_count_res->fetch_assoc()['total'];

// Latest quiz submission for this user/lecture
$quiz_sub = $conn->query("
  SELECT id, score FROM quiz_submissions
  WHERE user_id = $user_id AND lecture_id = $lecture_id
  ORDER BY id DESC LIMIT 1
")->fetch_assoc();

if ($quiz_sub) {
    // Count correct and incorrect answers
    $ans_summary = $conn->query("
      SELECT
        SUM(is_correct=1) AS correct_count,
        SUM(is_correct=0) AS incorrect_count
      FROM quiz_answers
      WHERE submission_id = {$quiz_sub['id']}
    ")->fetch_assoc();
    $correct = $ans_summary['correct_count'] ?? 0;
    $incorrect = $ans_summary['incorrect_count'] ?? 0;
    ?>
    <div class="quiz-summary-card" style="background:#eafff2; border-radius:14px;padding:20px;margin:17px 0; cursor:pointer;" onclick="window.location.href='quiz_review.php?lecture_id=<;?= $lecture_id ?>'">
      <div style="font-weight:600;color:#10b859;font-size:17px;">Quiz Completed!</div>
      <div style="margin-top:8px; font-size:15px;">Score: <b style="color:#1bba68;"><?= $quiz_sub['score'] ?>%</b></div>
      <div style="margin-top:6px;">Correct: <span style="color:#19b25b;"><?= $correct ?></span> | Incorrect: <span style="color:#ef5b48;"><?= $incorrect ?></span></div>
      <div style="margin-top:11px; color:#555;">Click for full stats and explanations</div>
    </div>
    <?php
} else {
    // Not yet submitted: ready to start
    ?>
    <div class="quiz-summary-card" style="background:#f4fff8; border-radius:14px; padding:19px;margin:17px 0; box-shadow:0 2px 7px #cefddb;">
      <div style="font-weight:600;font-size:16px;color:#219a50;">Quick Quiz</div>
      <div style="margin:5px 0 10px 0; font-size:14.2px;">Questions: <b><?= $q_count ?></b> • Flexible (No timer)</div>
      <a href="quiz.php?lecture_id=<?php echo $lecture_id; ?>"
        class="cw-download-btn"
        style="display:inline-block;margin-top:5px;width:120px;text-align:center;background:#10b859;color:#fff;">
        Take Quiz
      </a>
    </div>
    <?php
}
?>

<?php
// Practical/project block: assuming $prac comes from something like
// SELECT github_link, scored, score, feedback FROM practical_submissions WHERE user_id = $user_id AND lecture_id = $lecture_id

if ($lecture1['has_practical']): ?>
  <div class="practical-section" style="margin-bottom: 40px;">
    <h4>Practical Assignment</h4>
    <p>Create a responsive layout using Flexbox or Grid. Push your code to GitHub and provide your link below.</p>
   
    <?php if (!$prac || !$prac['scored']): ?>
      <form action="submit_practical.php" method="post">
          <input type="url" name="github_link" placeholder="Paste your GitHub repo link"
                 value="<?= $prac && $prac['github_link'] ? htmlspecialchars($prac['github_link']) : '' ?>" required class="github-input"/>
          <input type="hidden" name="lecture_id" value="<?= $lecture_id ?>">
          <button type="submit" class="cw-download-btn" style="margin-top:7px;">Submit Assignment</button>
      </form>
    <?php endif; ?>

    <?php if ($prac): ?>
      <div class="submission-card" style="margin-top:19px;">
        <b>Recent Submission:</b><br>
        <a href="<?= htmlspecialchars($prac['github_link']) ?>" class="submission-link"><?= htmlspecialchars($prac['github_link']) ?></a><br>
        <?= $prac['scored'] ? "<div style='margin-top:6px;'><b>Score:</b> {$prac['score']}/100</div>" : '<div style="margin-top:6px;">Awaiting grading...</div>' ?>
        <?php if (!empty($prac['feedback'])): ?>
          <div style="background:#f8fff0;border-left:4px solid #14e150;padding:10px 13px 8px;margin-top:10px;border-radius:7px;color:#1a6f28;font-size:0.97em;">
            <b>Feedback:</b> <?= nl2br(htmlspecialchars($prac['feedback'])) ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>


</div>
        </div>
</body>
</html>