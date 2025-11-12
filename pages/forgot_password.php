<?php
require_once '../backend/config.php';

$whatsappAdmin = "https://wa.me/2349025948400"; // update if needed

$email = trim($_POST['email'] ?? '');
$status = '';
$message = '';
$meta = [];
$showForm = true;

$forceRetry = isset($_GET['retry']) && $_GET['retry'] == 1 && !empty($_GET['email']);
if ($forceRetry && empty($_POST['email'])) {
    $_POST['email'] = $_GET['email'];
    $email = trim($_GET['email']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $forceRetry) {
    // 1. Does the user exist?
    $check = $conn->prepare("SELECT 1 FROM users WHERE email=? LIMIT 1");
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows == 0) {
        $status = "notfound";
        $message = "No account found for <b>$email</b>. Check your email or <a href='$whatsappAdmin' target='_blank'>contact admin</a>.";
        $meta = ["Request Email" => $email];
        $showForm = false;
        $check->close();
    } else {
        $check->close();

        // 2. Try to fetch the most recent reset for this email
        $stmt = $conn->prepare("SELECT token, sent_status, used, expires, created_at, sent_at, used_at
                                FROM password_resets
                                WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($token, $sent_status, $used, $expires, $created_at, $sent_at, $used_at);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $now = date('Y-m-d H:i:s');
            $meta = [
                "Request Email"  => $email,
                "Token"          => isset($token) ? substr($token, 0, 16) . "..." : "—",
                "Requested At"   => $created_at,
                "Expires"        => $expires,
                "Admin Sent At"  => $sent_at ?: "Not yet",
                "Used At"        => $used_at ?: "Not yet",
                "Status"         => ucfirst($sent_status)
            ];
            else {
    // This else block runs if the user has no reset record.
    // Also use this for the retry case.
    if ($forceRetry) {
        // Invalidate previous active tokens for this email
        $update = $conn->prepare("UPDATE password_resets SET used=1, sent_status='invalid' WHERE email=? AND used=0 AND sent_status IN ('sent','pending')");
        $update->bind_param('s', $email);
        $update->execute();
        $update->close();
    }
    // Now create the new reset record:
    $status = "pending";
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $created = date('Y-m-d H:i:s');
    $sent_status = 'pending';
    $used = 0;
    $stmt2 = $conn->prepare("INSERT INTO password_resets (email, token, expires, used, sent_status, created_at)
                             VALUES (?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param('sssiss', $email, $token, $expires, $used, $sent_status, $created);
    $stmt2->execute();
    $stmt2->close();
    $meta = [
        "Request Email"  => $email,
        "Token"          => substr($token, 0, 16) . "...",
        "Requested At"   => $created,
        "Expires"        => $expires,
        "Admin Sent At"  => "Not yet",
        "Used At"        => "Not yet",
        "Status"         => "Pending"
    ];
    $message = "Your request has been received and is pending.<br>
                <span class='meta-small'>Manual requests may take a few hours.</span>";
    $showForm = false;
}

            if ($used == 1 || $sent_status === "used" || $sent_status === "invalid") {
                $status = "used";
                $message = "Your previous reset link is no longer valid. <a href='forgot_password.php'>Request a new link</a>.";
            } else if ($sent_status === "pending") {
                $status = "pending";
                $message = "Your request is pending admin approval.<br>
                        <span class='meta-small'>Manual requests may take a few hours.</span>";
                $showForm = false;
            } else if ($sent_status === "sent" && $now < $expires) {
                $status = "sent";
                $message = "Reset link was sent to your email.<br>
                        <a href='forgot_password.php?retry=1&email=<?php echo urlencode($email); ?>'>Request another link</a> if you didn't receive it.";
                $showForm = false;
            } else {
                // expired or reusable, allow again
                $status = "expired";
                $message = "Previous reset link expired or invalid.<br>
                        <a href='forgot_password.php'>Request a new one</a>.";
            }
        } else {
            // No existing record, so create one and set up meta
            $status = "pending";
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            $created = date('Y-m-d H:i:s');
            $sent_status = 'pending';
            $used = 0;
            $stmt2 = $conn->prepare("INSERT INTO password_resets (email, token, expires, used, sent_status, created_at)
                                     VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param('sssiss', $email, $token, $expires, $used, $sent_status, $created);
            $stmt2->execute();
            $stmt2->close();
            $meta = [
                "Request Email"  => $email,
                "Token"          => substr($token, 0, 16) . "...",
                "Requested At"   => $created,
                "Expires"        => $expires,
                "Admin Sent At"  => "Not yet",
                "Used At"        => "Not yet",
                "Status"         => "Pending"
            ];
            $message = "Your request has been received and is pending.<br>
                        <span class='meta-small'>Manual requests may take a few hours.</span>";
            $showForm = false;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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
    .a-status-pending { background: #ffeaa3; color: #a88900;}
    .a-status-sent { background: #a3ffe6; color: #0aa074;}
    .a-status-used { background: #f8f8fc; color: #888; border:1.2px solid #e2e3ef;}
    .a-status-expired { background: #ffdbdb; color: #ce1a02; }
    .a-status-notfound { background:#ffd3df; color:#ab0033;}
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
    input[type="email"] {
      width: 100%; font-size:1.03em; padding: 10px 7px; border:1.2px solid #defae0;
      border-radius: 6px; margin-bottom: 18px; background:#f4fff9;
      margin-top:6px;outline:none;box-sizing:border-box;transition:border-color .14s;}
    input[type="email"]:focus{border-color:#0fae57;}
    .back-link {
      display:block;color:#10b859;text-align:center;margin-top:17px;font-weight:600;text-decoration:none;
    }
    .admin-contact {
      color:#099d44; font-weight:700; display:inline-block; text-decoration:none; margin-left:6px;
    }
    .admin-contact:hover { text-decoration:underline; color:#1ea958;}
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
<meta name="twitter:image" content="https://webdev.wasmer.app/assets/images/banner.png">

<meta property="og:title" content="Web Development for Beginners">
<meta property="og:description" content="Learn web development from scratch. Join our beginner-friendly course, get hands-on tutorials, and connect with a vibrant learning community.">
<meta property="og:image" content="https://webdev.wasmer.app/assets/images/banner.png">
<meta property="og:url" content="https://webdev.wasmer.app">
<meta property="og:type" content="website">
</head>
<body>
    <div class="aurora-card">
      <div class="a-header">
        <i class="fas fa-key"></i> Forgot Password
      </div>
      <?php
      if ($status) {
          $statClass = "a-status-" . htmlspecialchars($status);
          $statText = "";
          switch ($status) {
              case "pending": $statText = "Pending Admin Approval"; break;
              case "sent": $statText = "Link Sent, Check Email"; break;
              case "expired": $statText = "Link Expired"; break;
              case "used": $statText = "Link Already Used"; break;
              case "notfound": $statText = "No Account Found"; break;
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
        <label for="forgotEmail" style="font-weight:600;">Email Address</label>
        <input type="email" id="forgotEmail" name="email" required placeholder="your@email.com">
        <button type="submit" class="cta-button">Continue</button>
      </form>
      <?php } ?>
      <div class="a-footer">
        Need help?
        <a href="<?php echo $whatsappAdmin; ?>" target="_blank" class="admin-contact">
          <i class="fab fa-whatsapp"></i> Contact admin on WhatsApp
        </a>
        if you don’t receive a reset link within a reasonable time.
      </div>
      <a href="../index.php" class="back-link">Back to Login</a>
    </div>
    
    <!-- Floating WhatsApp Button -->
    <a class="float-contact" href="<?php echo $whatsappAdmin; ?>" target="_blank" title="Chat on WhatsApp">
      <i class="fab fa-whatsapp"></i> Contact
    </a>
</body>
</html>