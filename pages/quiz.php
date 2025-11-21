<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$lecture_id = isset($_GET['lecture_id']) ? intval($_GET['lecture_id']) : 0;

// Protect: must be logged in, valid lecture
if (!$user_id || !$lecture_id) {
    header("Location: dashboard.php");
    exit();
}

// Only fetch quiz questions if starting: ...?lecture_id=xx&start=1
$quiz_started = isset($_GET['start']) && $_GET['start'] == 1;

// Get lecture info
$lecture = $conn->query("SELECT title FROM lectures WHERE id=$lecture_id")->fetch_assoc();
$q_count = $conn->query("SELECT COUNT(*) AS total FROM quiz_questions WHERE lecture_id = $lecture_id")->fetch_assoc()['total'];

// Check if already submitted
$attempt = $conn->query("SELECT id FROM quiz_submissions WHERE user_id=$user_id AND lecture_id=$lecture_id")->fetch_assoc();
if ($attempt) {
    header("Location: quiz_review.php?lecture_id=$lecture_id");
    exit();
}

// Fetch questions if quiz started
if ($quiz_started) {
    $questions = $conn->query("SELECT * FROM quiz_questions WHERE lecture_id = $lecture_id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Quiz - <?= htmlspecialchars($lecture['title']) ?></title>
    <style>
        body {background:#f8f9fa;font-family:'Poppins','Segoe UI',sans-serif;}
        .quiz-container {max-width:500px;margin:40px auto;background:#fff;padding:36px 18px 28px;border-radius:22px;box-shadow:0 5px 22px #b8ffd9;}
        .quiz-header {font-size:1.22em;font-weight:700;color:#099a33;margin-bottom:29px;}
        .stat-bar {padding:10px 0 20px;color:#299050;}
        .quiz-btn {background:linear-gradient(90deg, #10b859 60%, #09e716 100%);
            color:#fff;font-weight:700;
            border-radius:13px;font-size:1.16em;padding:16px 0;width:80%;margin:0 auto 22px;display:block;cursor:pointer;
            border:none;box-shadow:0 2px 15px #b3ffcd90;transition:.19s;}
        .quiz-btn:hover {background:linear-gradient(90deg,#09e716 80%,#00d88e 100%);}
        .quiz-question {font-weight:700;margin:22px 0 12px;color:#135b39;}
        .quiz-option {margin: 0 0 10px 12px;}
        .quiz-option label {display:block;padding:10px 0 10px 7px;font-size:1.09em;background:#f6f8fa;border-radius:7px;cursor:pointer;}
        .quiz-option input[type=radio] {margin-right:11px;}
        @media (max-width:600px){.quiz-container{border-radius:15px;padding:20px 6vw;}}
    </style>
    <script>
        let quizStarted = <?= $quiz_started ? 'true' : 'false' ?>;
        function startQuiz() {
            window.location.href = "quiz.php?lecture_id=<?php echo $lecture_id; ?>&start=1";
        }
        // Navigation lock if quiz started
        window.onload = () => {
            if (quizStarted) {
              window.onbeforeunload = () => "Your quiz progress will be lost. Leave?";
              document.querySelectorAll('a').forEach(a=>{
                a.addEventListener('click', function(e){
                  e.preventDefault();
                  if(confirm("Leave? Your quiz will be lost.")) window.location=this.href;
                });
              });
            }
        };

window.onload = () => {
    if (quizStarted) {
        window.onbeforeunload = () => "Your quiz progress will be lost. Leave?";
        // Catch navigation on anchor tags
        document.querySelectorAll('a').forEach(a=>{
            a.addEventListener('click', function(e){
                e.preventDefault();
                if(confirm("Leave? Your quiz will be lost.")) window.location=this.href;
            });
        });
        // Add handler to quiz submit button for confirmation dialog
        let quizForm = document.getElementById('quizForm');
        if (quizForm) {
            quizForm.onsubmit = function(e) {
                // Custom confirmation dialog for submitting quiz
                if (!confirm("Are you sure you want to submit your quiz?")) {
                    e.preventDefault();
                    return false;
                }
                // Disable unload warning after submitting
                window.onbeforeunload = null;
            };
        }
    }
};
    </script>
</head>
<body>
<div class="quiz-container">
    <div class="quiz-header"><?= htmlspecialchars($lecture['title']) ?> – Quick Quiz</div>
    <?php if (!$quiz_started): ?>
        <div class="stat-bar">
            <b><?= $q_count ?> question<?= $q_count==1?'':'s' ?></b><br>
            No timer • Flexible<br>
            Ready to test what you learned?
        </div>
        <button class="quiz-btn" onclick="startQuiz()">Start Quiz</button>
    <?php else: ?>
        <form method="post" action="submit_quiz.php" id="quizForm">
            <?php $number = 1; while($q = $questions->fetch_assoc()): ?>
                <div class="quiz-question">Question <?= $number ?> of <?= $q_count ?>:<br><?= htmlspecialchars($q['question']) ?></div>
                <div class="quiz-option">
                    <label><input type="radio" name="ans[<?= $q['id'] ?>]" value="A" required> <?= htmlspecialchars($q['optiona']) ?></label>
                </div>
                <div class="quiz-option">
                    <label><input type="radio" name="ans[<?= $q['id'] ?>]" value="B"> <?= htmlspecialchars($q['optionb']) ?></label>
                </div>
                <?php if($q['optionc']): ?>
                <div class="quiz-option">
                    <label><input type="radio" name="ans[<?= $q['id'] ?>]" value="C"> <?= htmlspecialchars($q['optionc']) ?></label>
                </div>
                <?php endif; ?>
                <?php if($q['optiond']): ?>
                <div class="quiz-option">
                    <label><input type="radio" name="ans[<?= $q['id'] ?>]" value="D"> <?= htmlspecialchars($q['optiond']) ?></label>
                </div>
                <?php endif; ?>
            <?php $number++; endwhile; ?>
            <input type="hidden" name="lecture_id" value="<?= $lecture_id ?>">
            <button type="submit" class="quiz-btn" style="width:100%;">Submit Quiz</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>