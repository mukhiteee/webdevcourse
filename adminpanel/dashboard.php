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