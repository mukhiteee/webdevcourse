<?php
session_start();
require_once '../backend/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
$lecture_id = isset($_POST['lecture_id']) ? intval($_POST['lecture_id']) : 0;
$answers = $_POST['ans'] ?? [];

// Check authentication and valid lecture ID
if (!$user_id || !$lecture_id || empty($answers)) {
    header("Location: dashboard.php");
    exit();
}

// Prevent multiple submissions for same user/lecture
$already = $conn->query("SELECT id FROM quiz_submissions WHERE user_id=$user_id AND lecture_id=$lecture_id")->fetch_assoc();
if ($already) {
    header("Location: quiz_review.php?lecture_id=$lecture_id");
    exit();
}

// Prepare: fetch all relevant questions & answers
$qsql = "SELECT id, answer FROM quiz_questions WHERE lecture_id=$lecture_id";
$qres = $conn->query($qsql);

$total = 0;
$correct = 0;
$details = [];

while ($row = $qres->fetch_assoc()) {
    $qid = $row['id'];
    $user_choice = $answers[$qid] ?? '';
    $is_correct = ($user_choice == $row['answer']);
    $total++;
    if ($is_correct) $correct++;
    $details[] = [
        'qid' => $qid,
        'user_choice' => $user_choice,
        'is_correct' => $is_correct ? 1 : 0,
    ];
}

// Calculate score as percentage
$score = $total > 0 ? round(($correct * 100) / $total) : 0;

// Save submission
$conn->query("INSERT INTO quiz_submissions (user_id, lecture_id, score) VALUES ($user_id, $lecture_id, $score)");
$submission_id = $conn->insert_id;

// Save answers per question
$stmt = $conn->prepare("INSERT INTO quiz_answers (submission_id, question_id, user_choice, is_correct) VALUES (?, ?, ?, ?)");
foreach ($details as $item) {
    $stmt->bind_param('iisi', $submission_id, $item['qid'], $item['user_choice'], $item['is_correct']);
    $stmt->execute();
}
$stmt->close();

// Redirect to review page
header("Location: quiz_review.php?lecture_id=$lecture_id");
exit();
?>