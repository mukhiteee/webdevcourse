<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
      body {
        background: linear-gradient(120deg, #10b859 0%, #000096 100%);
        min-height: 100vh; display:flex; align-items:center; justify-content:center;
      }
      .crazy-card {
        max-width: 370px; margin:auto; padding:38px 24px 30px 24px;
        border-radius: 22px; background: rgba(255,255,255,.13);
        box-shadow: 0 8px 48px #10b85924; border: 2.5px solid #23ffc965;
        backdrop-filter: blur(2px); animation: floatUp 1s cubic-bezier(.845,0,.245,1.01);
      }
      @keyframes floatUp {
        0%{transform:translateY(44px) scale(.95);opacity:0;}
        80%{opacity:1;}
        100%{transform:translateY(0) scale(1);}
      }
      .bigicon {font-size:2.2em; color:#d2ff18; text-align:center; margin-bottom:8px;}
      .crazy-title {color:#fff;font-size:2em;font-weight:800;text-align:center;margin-bottom:21px;text-shadow:0 2px 10px #10b85958;}
      .crazy-field {margin-bottom:16px;}
      label {color:#e4ffe9;font-weight:700;}
      .crazy-input {width:100%;padding:12px 8px;border:2px solid #fff1;border-radius:9px;background:rgba(255,255,255,0.17);color:#fff;font-size:1.09em;outline:none;}
      .crazy-input:focus {border-color:#14ffc1;}
      .crazy-btn {width:100%;background:linear-gradient(90deg,#11faa6 60%,#19adff 100%);color:#003d27;font-weight:900;font-size:1.13em;border:none;border-radius:8px;padding:12px 0 12px 0;margin-top:6px;box-shadow:0 3px 15px #22f8a824;transition:background .22s, color .14s;}
      .crazy-btn:hover {background:linear-gradient(90deg,#19adff 68%,#10b859 100%);color:#fff;}
      .errbox {background:#ff2c4fda;color:#fff;padding:11px;font-weight:700;margin-bottom:21px;border-radius:7px;text-align:center;}
    </style>
</head>
<body>
<div class="crazy-card">
    <div class="bigicon"><i class="fas fa-user-secret"></i></div>
    <div class="crazy-title">Admin Login</div>
    <?php if ($error): ?><div class="errbox"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" action="login.php" autocomplete="off">
        <div class="crazy-field">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="crazy-input" required autofocus>
        </div>
        <div class="crazy-field">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="crazy-input" required>
        </div>
        <button class="crazy-btn" type="submit">Log In</button>
    </form>
</div>
</body>
</html>