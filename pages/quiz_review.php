<?php
session_start();
require_once '../backend/config.php';

$user_id = $_SESSION['user_id'] ?? 0;

if (!isset($_GET['id'])) {
    header ('Location: dashboard.php');
}

$lecture_id = isset($_GET['lecture_id']) ? intval($_GET['lecture_id']) : 1;

// Fetch the user's latest submission for this lecture
$sub_res = $conn->query(
    "SELECT id, score, submission_time FROM quiz_submissions WHERE user_id=$user_id AND lecture_id=$lecture_id ORDER BY id DESC LIMIT 1"
);
$submission = $sub_res->fetch_assoc();
if(!$submission) {
    header("Location: lecture.php?id=$lecture_id");
    exit();
}
$submission_id = $submission['id'];

// Get lecture info
$lecture = $conn->query("SELECT title FROM lectures WHERE id=$lecture_id")->fetch_assoc();

// Fetch all quiz answers for this submission, joined with question info
$answers_res = $conn->query(
    "SELECT a.question_id, a.user_choice, a.is_correct, q.question, q.optiona, q.optionb, q.optionc, q.optiond, q.answer, q.explanation
     FROM quiz_answers a
     JOIN quiz_questions q ON a.question_id = q.id
     WHERE a.submission_id = $submission_id
     ORDER BY a.question_id"
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz Review - <?= htmlspecialchars($lecture['title']) ?></title>
    <style>
        body {background:#f8f9fa;font-family:'Poppins','Segoe UI',sans-serif;}
        .review-container {max-width:540px;margin:36px auto 0;background:#fff;padding:32px 18px 30px;border-radius:22px;box-shadow:0 7px 33px #b7f4d7;}
        .review-header {font-size:1.2em;font-weight:700;color:#06a159;margin-bottom:15px;}
        .score-badge {margin:16px 0 20px;width:92px;height:92px;font-size:2em;font-weight:800;color:#fff;border-radius:50%;background:linear-gradient(90deg,#10b859 70%,#09e716 100%);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 17px #bdffe4;}
        .qa-block {margin:21px 0 23px;padding:13px 17px 15px;border-radius:13px;background:#f7fffd;box-shadow:0 1px 8px #eaffee;}
        .qa-title {font-weight:bold;margin-bottom:7px;font-size:1.08em;}
        .correct {color:#04a062;}
        .wrong {color:#e03c17;}
        .selected {font-weight:700;}
        .option-row {margin-top:4px;margin-bottom:4px;}
        .exp-block {background:#eefff6;margin:12px 0 0;padding:11px 10px 8px;border-radius:8px;font-size:0.99em;color:#36844c;}
        .retake-btn {background:#09e716;color:#fff;font-weight:700;border-radius:10px;padding:12px 0;width:90%;margin:23px auto 7px;display:block;font-size:1.13em;border:none;box-shadow:0 1px 9px #bdffe8;transition:.16s;cursor:pointer;text-align:center;}
        .retake-btn:hover{background:#07b312;}
        .score-label {margin-bottom:9px;}
        .submission-time {font-size:0.98em;margin-bottom:15px;color:#409555;}
        @media (max-width:600px){.review-container{padding:6vw 3vw;}}
    </style>
</head>
<body>
<div class="review-container">
    <div class="review-header">Quiz Result: <?= htmlspecialchars($lecture['title']) ?></div>
    <div class="score-badge"><?= $submission['score'] ?>%</div>
    <div class="score-label"><?= $submission['score'] >= 70 ? "Great job!" : "Keep practicing!" ?></div>
    <div class="submission-time"><?= date('M j, Y h:ia', strtotime($submission['submission_time'])) ?></div>
    <?php
    $qnum = 1;
    while($a = $answers_res->fetch_assoc()):
        $is_user_correct = $a['is_correct'];
        $user_choice = $a['user_choice'];
        ?>
        <div class="qa-block">
            <div class="qa-title">
                Question <?= $qnum ?>:
                <?= htmlspecialchars($a['question']) ?>
                <?php if($is_user_correct): ?>
                    <span class="correct">&#10003; Correct</span>
                <?php else: ?>
                    <span class="wrong">&#10005; Incorrect</span>
                <?php endif; ?>
            </div>
            <div class="option-row<?= $user_choice=='A' ? ' selected' : '' ?>">
                A. <?= htmlspecialchars($a['optiona']) ?>
                <?= ($a['answer']=='A') ? '<span class="correct"> (Correct)</span>' : '' ?>
                <?= ($user_choice=='A' && $a['answer']!='A') ? '<span class="wrong"> (Your Choice)</span>' : '' ?>
            </div>
            <div class="option-row<?= $user_choice=='B' ? ' selected' : '' ?>">
                B. <?= htmlspecialchars($a['optionb']) ?>
                <?= ($a['answer']=='B') ? '<span class="correct"> (Correct)</span>' : '' ?>
                <?= ($user_choice=='B' && $a['answer']!='B') ? '<span class="wrong"> (Your Choice)</span>' : '' ?>
            </div>
            <?php if($a['optionc']): ?>
            <div class="option-row<?= $user_choice=='C' ? ' selected' : '' ?>">
                C. <?= htmlspecialchars($a['optionc']) ?>
                <?= ($a['answer']=='C') ? '<span class="correct"> (Correct)</span>' : '' ?>
                <?= ($user_choice=='C' && $a['answer']!='C') ? '<span class="wrong"> (Your Choice)</span>' : '' ?>
            </div>
            <?php endif; ?>
            <?php if($a['optiond']): ?>
            <div class="option-row<?= $user_choice=='D' ? ' selected' : '' ?>">
                D. <?= htmlspecialchars($a['optiond']) ?>
                <?= ($a['answer']=='D') ? '<span class="correct"> (Correct)</span>' : '' ?>
                <?= ($user_choice=='D' && $a['answer']!='D') ? '<span class="wrong"> (Your Choice)</span>' : '' ?>
            </div>
            <?php endif; ?>
            <?php if($a['explanation']): ?>
            <div class="exp-block">
                <b>Explanation:</b> <?= htmlspecialchars($a['explanation']) ?>
            </div>
            <?php endif; ?>
        </div>
    <?php $qnum++; endwhile; ?>
    <form method="get" action="retake.php">
        <input type="hidden" name="lecture_id" value="<?= $lecture_id ?>">
        <button type="submit" class="retake-btn">Return to Dashboard</button>
    </form>
</div>
</body>
</html>