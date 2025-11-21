<?php
session_start();
require_once '../backend/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
  header('Location: ../index.php');
  exit();
}

// Fetch notifications: All globals + all for this user, order newest first
$notis = $conn->query(
  "SELECT * FROM notifications WHERE user_id IS NULL OR user_id=$user_id ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications | CodeWithMukhiteee</title>
  <style>
    body { background:#f8f9fa; font-family:'Poppins','Segoe UI',sans-serif; }
    .notif-panel { max-width:510px;margin:29px auto 0;background:#fff;padding:22px 16px;border-radius:20px;box-shadow:0 6px 24px #bafad9; }
    .notif-title { font-size:1.2em;font-weight:700;margin-bottom:10px;color:#10b859;}
    .notif-card {background:#f7fff9; border-left:5px solid #13ce65; border-radius:9px; padding:14px 15px 11px;margin-bottom:16px; box-shadow:0 1px 7px #d0ffe7; display: flex; gap:12px; align-items: flex-start;}
    .notif-icon {font-size:1.6em; margin:2px 0 0 0;}
    .notif-card.success {border-color:#25c971;}
    .notif-card.warning { border-color: #ffd740; background: #fffbe0;}
    .notif-card.error { border-color: #ef3f3b; background: #ffebeb;}
    .notif-card.info { border-color: #17b6ea; background:#f0fbff;}
    .notif-card.reminder { border-color: #fdc04e; background: #fff8ec;}
    .notif-content {flex:1;}
    .notif-heading {font-weight:bold;font-size:1em;margin-bottom:6px;}
    .notif-date {font-size:.94em;color:#888;margin-top:2px;}
    .notif-link {color:#10b859;font-weight:600;text-decoration:underline;}
    .notif-unread {background:#e6ffe6;}
  </style>
  <script>
    // For "mark as read" AJAX, or auto-fade new notis if you want
  </script>
</head>
<body>
  <div class="notif-panel">
    <div class="notif-title"><i class="fas fa-bell"></i> Notifications</div>
    <?php
    $icon_map = [
      'info'=>'fa-info-circle', 'success'=>'fa-check-circle',
      'warning'=>'fa-exclamation-triangle', 'error'=>'fa-times-circle',
      'reminder'=>'fa-bell'
    ];
    while($n = $notis->fetch_assoc()): ?>
      <div class="notif-card <?= $n['type'] ?> <?= $n['is_read']?'':'notif-unread' ?>">
        <div class="notif-icon"><i class="fas <?= $icon_map[$n['type']] ?? 'fa-info-circle' ?>"></i></div>
        <div class="notif-content">
          <div class="notif-heading"><?= htmlspecialchars($n['title']) ?></div>
          <div><?= nl2br(htmlspecialchars($n['message'])) ?></div>
          <?php if ($n['link']): ?>
            <div><a href="<?= htmlspecialchars($n['link']) ?>" target="_blank" class="notif-link">Read More</a></div>
          <?php endif; ?>
          <div class="notif-date"><?= date('M j, Y h:ia', strtotime($n['created_at'])) ?></div>
        </div>
      </div>
    <?php endwhile; ?>
    <?php if($notis->num_rows == 0): ?>
      <div style="text-align:center;color:#96a187;">No notifications yet!</div>
    <?php endif; ?>
  </div>
</body>
</html>