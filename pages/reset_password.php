<?php
require_once '../backend/config.php';

$token = trim($_GET['token'] ?? '');
$status = '';
$message = '';
$meta = [];
$showForm = false;

// If a token is present, validate it
if ($token) {
    $stmt = $conn->prepare("SELECT email, expires, used, sent_status, used_at FROM password_resets WHERE token=? LIMIT 1");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($email, $expires, $used, $sent_status, $used_at);

    if ($stmt->num_rows == 0) {
        $status = "invalid";
        $message = "This reset link is invalid. Please request a new reset link.";
    } else {
        $stmt->fetch();
        $now = date('Y-m-d H:i:s');
        $meta = [
            "Email"     => $email,
            "Token"     => substr($token, 0, 16) . "...",
            "Expires"   => $expires,
            "Used At"   => $used_at ?: "Not yet",
            "Status"    => ucfirst($sent_status)
        ];
        if ($used == 1 || $sent_status === "used" || $sent_status === "invalid") {
            $status = "used";
            $message = "This reset link has already been used or is no longer valid.";
        } elseif ($now > $expires) {
            $status = "expired";
            $message = "This reset link has expired. <a href='forgot_password.php'>Request a new one</a>.";
        } elseif ($sent_status !== "sent") {
            $status = "pending";
            $message = "This reset link is pending admin approval. Wait for admin to send your link.";
        } else {
            // Valid and ready to use
            $status = "active";
            $showForm = true;
        }
    }
    $stmt->close();
}

// Handle POST (password update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['new_password'], $_POST['confirm_password'])) {
    $token = trim($_POST['token']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $status = "error";
        $message = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $status = "error";
        $message = "Password should be at least 6 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT email, expires, used, sent_status FROM password_resets WHERE token=? LIMIT 1");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($email, $expires, $used, $sent_status);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $now = date('Y-m-d H:i:s');
            if ($used == 1 || $sent_status !== "sent" || $now > $expires) {
                $status = "error";
                $message = "This reset link is no longer valid. Please request a new one.";
            } else {
                // All good, reset password
                $hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in users db
                $stmt2 = $conn->prepare("UPDATE users SET password=? WHERE email=?");
                $stmt2->bind_param('ss', $hash, $email);
                $stmt2->execute();
                $stmt2->close();

                // Mark reset token as used
                $stmt3 = $conn->prepare("UPDATE password_resets SET used=1, sent_status='used', used_at=NOW() WHERE token=?");
                $stmt3->bind_param('s', $token);
                $stmt3->execute();
                $stmt3->close();

                $status = "success";
                $message = "Your password has been successfully reset! <a href='../index.php'>Login now</a>.";
                $meta = ["Email"=>$email, "Status"=>"Used"];
            }
        } else {
            $status = "error";
            $message = "Invalid or expired reset link.";
        }
        $stmt->close();
    }
    $showForm = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <style>
    .aurora-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 16px #10b85918;
      max-width: 420px;
      margin: 34px auto 0;
      padding: 28px 18px 21px 18px;
      color: #214d35;
      font-family: 'Poppins', Arial, sans-serif;
      border: 2px solid #dbffe7;
      overflow-x: auto;
    }
    .a-header {
      font-weight: 900;
      font-size:1.23em; margin-bottom:11px; text-align:center;letter-spacing:.04em;
      color:#0ea969; display:flex; align-items:center;justify-content:center;gap:7px;
    }
    .a-status {
      display: inline-block;
      padding: 4px 25px 4px 13px;
      border-radius: 10px 10px 10px 3px;
      font-size: 1em; font-weight:700; margin-bottom:15px;
    }
    .a-status-active { background:#a3ffe6;color:#0aa074;}
    .a-status-success {background:#e4ffcb;color:#128723;}
    .a-status-pending {background:#ffeaa3;color:#a88900;}
    .a-status-sent   {background:#a3ffe6;color:#0aa074;}
    .a-status-used   {background:#f8f8fc;color:#888;}
    .a-status-error  {background:#ffe2ea;color:#a4032d;}
    .a-status-expired{background:#ffdbdb;color:#ce1a02;}
    .a-status-invalid{background:#ffd3df;color:#ab0033;}
    .a-meta-row {
      display: flex; justify-content: space-between; gap:9px; margin-bottom:7px;
    }
    .a-meta-label {
      color: #858fa4; font-weight: 600; font-size: 0.97em;
    }
    .a-meta-value {
      color: #214d35; font-weight: 700; font-size: 0.99em;
    }
    .a-msg {
      text-align:center;margin:22px 1px 7px 1px;
      font-size: 1.08em;font-weight:500;
    }
    .meta-small {
      color:#888; font-size:.94em;
    }
    .a-footer {
      margin-top:17px;font-size:.98em;text-align:center;color:#175233;
    }
    .cta-button {
      background: linear-gradient(90deg,#10b859 70%,#19ffc9 120%);
      color: #fff; width:100%; border:none; border-radius: 9px;
      font-weight: 800; font-size: 1.09em; padding: 12px 0 10px;
      margin-top: 10px; cursor: pointer; margin-bottom:12px; box-shadow:0 1px 9px #0bea5e16;
      transition: background .14s,box-shadow .13s;
    }
    .cta-button:hover { background: #0dbc64; box-shadow:0 2px 13px #19ffc644;}
    input[type="password"] {
      width: 100%; font-size:1.03em; padding: 10px 7px; border:1.2px solid #defae0;
      border-radius: 6px; margin-bottom: 18px; background:#f4fff9;
      margin-top:6px;outline:none;box-sizing:border-box;transition:border-color .14s;}
    input[type="password"]:focus{border-color:#0fae57;}
    .back-link {
      display:block;color:#10b859;text-align:center;margin-top:17px;font-weight:600;text-decoration:none;
    }

    .float-contact {
      position: fixed; right: 20px; bottom: 23px; background:#25d366;
      color:#fff; font-weight:700; z-index:2000; padding:15px;
      border-radius:60px; box-shadow:0 3px 20px #0d7d383a;
      font-size:27px; display:flex; align-items:center; gap:12px;
      text-decoration:none; transition:background .13s;
      border:2.2px solid #fff
    }
    .float-contact:hover{background:#21b75c; color:#fff;}
    @media (max-width: 600px) {
      .float-contact {font-size:22px; padding:12px 14px;}
    }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../assets/images/icon.png" type="image/png">
    <link rel="apple-touch-icon" href="../assets/images/icon.png">
    <link rel="manifest" href="../manifest.json">
    <meta name="theme-color" content="#10b859">
<meta name="description" content="Learn web development from scratch. Join our beginner-friendly course, get hands-on tutorials, and connect with a vibrant learning community.">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Web Development for Beginners">
<meta name="twitter:description" content="Learn web development from scratch. Join our beginner-friendly course, get hands-on tutorials, and connect with a vibrant learning community.">
<meta name="twitter:image" content="https://webdev.wasmer.app/assets/images/banner.jpg">

<meta property="og:title" content="Web Development for Beginners">
<meta property="og:description" content="Learn web development from scratch. Join our beginner-friendly course, get hands-on tutorials, and connect with a vibrant learning community.">
<meta property="og:image" content="https://webdev.wasmer.app/assets/images/banner.jpg">
<meta property="og:url" content="https://webdev.wasmer.app">
<meta property="og:type" content="website">
</head>
<body>
    <div class="aurora-card">
      <div class="a-header">
        <i class="fas fa-lock"></i> Reset Password
      </div>
      <?php
      if ($status) {
          $statClass = "a-status-" . htmlspecialchars($status);
          $statText = "";
          switch ($status) {
              case "active": $statText = "Reset Link Valid"; break;
              case "success": $statText = "Password Reset Successfully"; break;
              case "pending": $statText = "Link Not Yet Sent"; break;
              case "sent": $statText = "Reset Link Ready"; break;
              case "expired": $statText = "Link Expired"; break;
              case "used": $statText = "Link Already Used"; break;
              case "error": $statText = "Error: Check Below"; break;
              case "invalid": $statText = "Invalid Link"; break;
              default: $statText = ucfirst($status); break;
          }
          echo "<div class='a-status $statClass'>$statText</div>";
      }
      if (!empty($meta)) {
          foreach($meta as $label=>$value) {
              echo "<div class='a-meta-row'><div class='a-meta-label'>$label</div><div class='a-meta-value'>$value</div></div>";
          }
      }
      if ($message) echo "<div class='a-msg'>$message</div>";
      ?>
      <?php if ($showForm) { ?>
      <form method="POST" action="">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="newPass" style="font-weight:600;">New Password</label>
        <input type="password" id="newPass" name="new_password" required minlength="6" placeholder="Enter new password">
        <label for="confirmPass" style="font-weight:600;">Confirm Password</label>
        <input type="password" id="confirmPass" name="confirm_password" required minlength="6" placeholder="Repeat password">
        <button type="submit" class="cta-button">Change Password</button>
      </form>
      <?php } ?>
      <a href="../index.php" class="back-link">Back to Login</a>
    </div>
    
    <!-- Floating WhatsApp Button -->
    <a class="float-contact" href="https://wa.me/2349025948400" target="_blank" title="Contact">
      <i class="fab fa-whatsapp"></i> Contact
    </a>
</body>
</html>