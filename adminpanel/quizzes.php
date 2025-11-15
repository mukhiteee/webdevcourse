<?php
session_start();
require_once '../backend/config.php';
if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: index.php');
    exit;
}
$admin_username = ($_SESSION['admin_username']);
// CRUD operations
if (isset($_POST['add_quiz'])) {
    $lecture_id = intval($_POST['lecture_id']);
    $question = trim($_POST['question']);
    $optiona = trim($_POST['optiona']);
    $optionb = trim($_POST['optionb']);
    $optionc = trim($_POST['optionc']);
    $optiond = trim($_POST['optiond']);
    $answer = strtoupper(trim($_POST['answer']));
    $explanation = trim($_POST['explanation']);
    if ($lecture_id && $question && $optiona && $optionb && $answer) {
        $stmt = $conn->prepare("INSERT INTO quiz_questions (lecture_id, question, optiona, optionb, optionc, optiond, answer, explanation)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isssssss', $lecture_id, $question, $optiona, $optionb, $optionc, $optiond, $answer, $explanation);
        $stmt->execute(); $stmt->close();
    }
    header("Location: quizzes.php"); exit;
}
if (isset($_POST['update_quiz'])) {
    $id = intval($_POST['id']);
    $lecture_id = intval($_POST['lecture_id']);
    $question = trim($_POST['question']);
    $optiona = trim($_POST['optiona']);
    $optionb = trim($_POST['optionb']);
    $optionc = trim($_POST['optionc']);
    $optiond = trim($_POST['optiond']);
    $answer = strtoupper(trim($_POST['answer']));
    $explanation = trim($_POST['explanation']);
    if ($id && $lecture_id && $question && $optiona && $optionb && $answer) {
        $stmt = $conn->prepare("UPDATE quiz_questions SET lecture_id=?, question=?, optiona=?, optionb=?, optionc=?, optiond=?, answer=?, explanation=? WHERE id=?");
        $stmt->bind_param('isssssssi', $lecture_id, $question, $optiona, $optionb, $optionc, $optiond, $answer, $explanation, $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: quizzes.php"); exit;
}
if (isset($_POST['delete_quiz'])) {
    $id = intval($_POST['id']);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute(); $stmt->close();
    }
    header("Location: quizzes.php"); exit;
}
// Get all lectures for dropdown
$lectures = [];
$res2 = $conn->query("SELECT id, title FROM lectures");
while ($row = $res2->fetch_assoc()) $lectures[] = $row;

// Get all quiz questions
$quizzes = [];
$res = $conn->query("SELECT * FROM quiz_questions ORDER BY lecture_id, id");
while ($row = $res->fetch_assoc()) $quizzes[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quiz Management | Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../styles/admindash.css">
<style>
body {background:#f4fff9;font-family:'Poppins',sans-serif;color:#233e27;}
.mainbox {max-width:1130px;margin:46px auto 0;padding:20px 11px;}
.q-head {font-size:2em;font-weight:900;color:#10b859;margin-bottom:11px;display:flex;align-items:center;gap:12px;}
.quiz-table {width:100%;border-collapse:collapse;box-shadow:0 3px 17px #10b85918;border-radius:12px;overflow:hidden;background:#fff;}
.quiz-table th, .quiz-table td {padding:10px 8px;text-align:left;vertical-align:top;}
.quiz-table th {background:#ecfff2;color:#108c43;font-weight:700;}
.quiz-table tbody tr:nth-child(even){background:#f9fffa;}
.quiz-table td {font-size:.98em;}
.qform input, .qform select, .qform textarea {
  padding:6px 8px;border-radius:7px;border:1px solid #bffdd0;font-size:.99em;width:100%;background:#f9fffd;
  margin-bottom:3px;
}
.qform textarea{resize:vertical;min-height:40px;font-family:'Poppins',sans-serif;}
.qform button, .add-quiz-form button {
  background:#10b859;color:#fff;font-weight:700;border:none;border-radius:7px;padding:7px 12px;cursor:pointer;transition:.14s;}
.qform button:hover, .add-quiz-form button:hover {background:#1b7e44;}
.add-quiz-form {margin-top:28px;background:#fff;padding:14px 11px 10px 12px;border-radius:11px;border:1.8px solid #10b85946;max-width:760px;}
.add-quiz-form h4 {margin-top:0;color:#10b859;}
@media(max-width:930px){.mainbox{padding:4px;}.quiz-table th,.quiz-table td{padding:5px 3px;font-size:.9em;}}
</style>
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
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-chart-bar"></i></span> <span>Dashboard</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-users"></i></span> <span>Users</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-book"></i></span> <span>Lectures</span></a>
      <a class="nav-item active"><span class="nav-icon"><i class="fa fa-question"></i></span> <span>Quizzes</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-project-diagram"></i></span> <span>Projects</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-bell"></i></span> <span>Notifications</span></a>
      <a class="nav-item"><span class="nav-icon"><i class="fa fa-cog"></i></span> <span>Settings</span></a>
      <a class="nav-item" style="color:#e82c37;"><span class="nav-icon"><i class="fa fa-sign-out"></i></span> <span>Logout</span></a>
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="main-content">
    <h1 class="welcome-title">Welcome back, <?php echo $admin_username ?>!</h1>
    <p class="welcome-subtitle">Here’s what's happening with your platform today</p>

    
  <div class="q-head"><i class="fa fa-star"></i>Quiz Management</div>
  <table class="quiz-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Lecture</th>
        <th>Question</th>
        <th>Option A</th>
        <th>Option B</th>
        <th>Option C</th>
        <th>Option D</th>
        <th>Answer</th>
        <th>Explanation</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($quizzes as $q): ?>
      <tr>
        <form class="qform" method="post">
          <td><?php echo $q['id']; ?><input type="hidden" name="id" value="<?php echo $q['id']; ?>"></td>
          <td>
            <select name="lectureid" required>
              <?php foreach($lectures as $l): ?>
                <option value="<?php echo $l['id']; ?>"<?php if($l['id']==$q['lecture_id'])echo' selected';?>><?php echo htmlspecialchars($l['title']);?></option>
              <?php endforeach;?>
            </select>
          </td>
          <td><textarea name="question" required><?php echo htmlspecialchars($q['question']); ?></textarea></td>
          <td><textarea name="optiona" required><?php echo htmlspecialchars($q['optiona']); ?></textarea></td>
          <td><textarea name="optionb" required><?php echo htmlspecialchars($q['optionb']); ?></textarea></td>
          <td><textarea name="optionc"><?php echo htmlspecialchars($q['optionc']); ?></textarea></td>
          <td><textarea name="optiond"><?php echo htmlspecialchars($q['optiond']); ?></textarea></td>
          <td><input type="text" name="answer" value="<?php echo htmlspecialchars($q['answer']); ?>" maxlength="1" style="width:38px;" required></td>
          <td><textarea name="explanation"><?php echo htmlspecialchars($q['explanation']); ?></textarea></td>
          <td>
            <button name="update_quiz" value="1" title="Save"><i class="fa fa-save"></i></button>
            <button name="delete_quiz" value="1" onclick="return confirm('Delete this quiz?');" title="Delete"><i class="fa fa-trash"></i></button>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <!-- Add Quiz -->
  <form method="post" class="add-quiz-form">
    <h4>Add New Quiz Question</h4>
    <select name="lectureid" required>
      <option value="">--Select Lecture--</option>
      <?php foreach($lectures as $l): ?>
        <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['title']); ?></option>
      <?php endforeach;?>
    </select>
    <input type="text" name="answer" placeholder="Correct Option (A/B/C/D)" maxlength="1" required>
    <textarea name="question" placeholder="Question" required></textarea>
    <textarea name="optiona" placeholder="Option A" required></textarea>
    <textarea name="optionb" placeholder="Option B" required></textarea>
    <textarea name="optionc" placeholder="Option C"></textarea>
    <textarea name="optiond" placeholder="Option D"></textarea>
    <textarea name="explanation" placeholder="Explanation"></textarea>
    <button name="add_quiz" value="1"><i class="fa fa-plus"></i> Add Quiz</button>
  </form>
  </main>