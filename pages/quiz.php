<?php
session_start();
require_once '../backend/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
$lecture_id = isset($_GET['lecture_id']) ? intval($_GET['lecture_id']) : 0;

// Redirect if not logged in or invalid lecture
if (!$user_id) {
    header("Location: dashboard.php"); exit();
}

// Check if user has already submitted this quiz
$qs = $conn->query("SELECT id FROM quiz_submissions WHERE user_id=$user_id AND lecture_id=$lecture_id LIMIT 1");
if ($qs->num_rows) {
    header("Location: quiz_review.php?lecture_id=lecture_id"); exit();
}

// Fetch all questions for this lecture
$questions = $conn->query("SELECT * FROM quiz_questions WHERE lecture_id=$lecture_id");
if ($questions->num_rows == 0) {
    // No quiz available
    header("Location: lecture.php?id=lecture_id"); exit();
}

$lecture = $conn->query("SELECT title FROM lectures WHERE id=$lecture_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Quiz — <?= htmlspecialchars($lecture['title']) ?></title>
    <style>
        body {background:#f8f9fa;font-family:'Poppins','Segoe UI',sans-serif;}
        .quiz-container {max-width:520px;margin:36px auto 0;background:#fff;padding:32px 18px;border-radius:20px;box-shadow:0 4px 24px rgba(16,216,118,.09);}
        .quiz-header {font-size:1.3em;font-weight:700;color:#067033;margin-bottom:13px;}
        .quiz-question {font-weight:bold;margin:15px 0 7px;color:#135b39;}
        .quiz-option {margin: 0 0 9px 16px;}
        .quiz-submit-btn {background:#09e716;color:#fff;font-weight:600;padding:13px 0;width:98%;border:none;border-radius:10px;font-size:1.13em;margin:12px auto 7px;box-shadow:0 1px 7px #b1f7c0;cursor:pointer;display:block;}
        .lock-overlay {position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.45);color:#fff;z-index:1990;display:none;align-items:center;justify-content:center;font-size:1.35em;}
        .confirm-modal {background:#fff;color:#070a20;padding:32px 25px;border-radius:15px;text-align:center;box-shadow:0 4px 20px #00b86a44;}
        .modal-btn {background:#09e716;padding:10px 24px;color:#fff;border:none;border-radius:9px;margin:18px 7px;font-weight:600;cursor:pointer;}
        .modal-btn.secondary {background:#f1f4f3;color:#05814a;}
    </style>
    <script>
        // Lock navigation away
        let quizDirty = true;
        window.onbeforeunload = function() {
            if (quizDirty) return "Your progress will be canceled. Leave?";
        };
        // Warn on link navigation
        document.addEventListener('DOMContentLoaded', ()=>{
          document.querySelectorAll('a').forEach(a=>{
            a.addEventListener('click', function(e){
              if(!this.classList.contains('allow-nav') && quizDirty){
                e.preventDefault();
                showModal(this.href);
              }
            });
          });
        });
        function showModal(leaveUrl){
            document.getElementById('lockOverlay').style.display='flex';
            document.getElementById('modalLeave').onclick=function(){ quizDirty=false; window.location=leaveUrl; };
            document.getElementById('modalStay').onclick=function(){ document.getElementById('lockOverlay').style.display='none'; };
        }
    </script>
</head>
<body>
<div class="quiz-container">
    <div class="quiz-header"><?= htmlspecialchars($lecture['title']) ?> – Quiz</div>
    <form action="submit_quiz.php" method="POST" id="quizForm">
        <?php $qno=1; while ($q = $questions->fetch_assoc()): ?>
            <div class="quiz-question">Question <?= $qno ?>: <?= htmlspecialchars($q['question']) ?></div>
            <div class="quiz-option"><label>
                <input type="radio" name="ans[<?= $q['id'] ?>]" value="A" required> <?= htmlspecialchars($q['optiona']) ?>
            </label></div>
            <div class="quiz-option"><label>
                <input type="radio" name="ans[<?= $q['id'] ?>]" value="B"> <?= htmlspecialchars($q['optionb']) ?>
            </label></div>
            <?php if($q['optionc']): ?><div class="quiz-option"><label>
                <input type="radio" name="ans[<?= $q['id'] ?>]" value="C"> <?= htmlspecialchars($q['optionc']) ?>
            </label></div><?php endif; ?>
            <?php if($q['optiond']): ?><div class="quiz-option"><label>
                <input type="radio" name="ans[<?= $q['id'] ?>]" value="D"> <?= htmlspecialchars($q['optiond']) ?>
            </label></div><?php endif; ?>
        <?php $qno++; endwhile; ?>
        <input type="hidden" name="lecture_id" value="<?= $lecture_id ?>">
        <button type="submit" class="quiz-submit-btn" onclick="quizDirty=false;">Submit Quiz</button>
    </form>
</div>

<!-- Navigation lock modal -->
<div class="lock-overlay" id="lockOverlay">
  <div class="confirm-modal">
    <div style="font-weight:bold;font-size:1.13em;margin-bottom:11px;">Leave page?</div>
    <div style="margin-bottom:17px;">Your quiz progress will be lost! This action cannot be undone.</div>
    <button class="modal-btn" id="modalLeave">Yes, Leave</button>
    <button class="modal-btn secondary" id="modalStay">Stay</button>
  </div>
</div>
</body>
</html>