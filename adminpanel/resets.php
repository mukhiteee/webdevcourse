<?php
session_start();
require_once '../backend/config.php';
if (!($_SESSION['admin_logged_in'] ?? false)) { header('Location: index.php'); exit; }

// Optional search by email
$filter = trim($_GET['q'] ?? '');
$fe = $filter ? "WHERE email LIKE '%$filter%'" : "";

// List all active (not yet used/expired) reset requests
$resets = [];
$sql = "SELECT * FROM password_resets $fe ORDER BY created_at DESC";
$res = $conn->query($sql);
while($row = $res->fetch_assoc()) $resets[] = $row;

// Handle mark as used or invalidate
if ($_SERVER['REQUEST_METHOD']=='POST') {
  $id = intval($_POST['id'] ?? 0);
  if (isset($_POST['set_used'])) {
    $conn->query("UPDATE password_resets SET used=1, sent_status='used', used_at=NOW() WHERE id=$id");
  }
  if (isset($_POST['invalidate'])) {
    $conn->query("UPDATE password_resets SET sent_status='invalid' WHERE id=$id");
  }
  header("Location: resets.php?status=updated");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Password Reset Management | Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {background:#f4fff9;font-family:'Poppins',sans-serif;color:#233e27;}
.reset-wrap {max-width:900px;margin:52px auto 0;padding:24px 8px;}
.reset-head{font-size:2em;font-weight:900;color:#10b859;margin-bottom:24px;display:flex;align-items:center;gap:13px;}
.resets-table{width:100%;border-collapse:collapse;box-shadow:0 4px 18px #10b85916;border-radius:13px;background:#fff;}
.resets-table th,.resets-table td{padding:11px 7px;text-align:left;}
.resets-table th {background:#e7fff3;color:#108c43;font-weight:700;}
.resets-table tbody tr:nth-child(even){background:#f6fffa;}
.reset-status-badge {
  font-weight:700;border-radius:11px;font-size:.98em;padding:4px 15px;display:inline-block;
}
.pending {background:#edf8fb;color:#197b43;}
.sent {background:#c4f3cd;color:#108c43;}
.used {background:#d1e7fd;color:#59a9de;}
.invalid {background:#fccccc;color:#bf353a;}
.expired {background:#ffe4c7;color:#bd5609;}
.action-btn {
  background:#10b859;color:#fff;font-weight:800;border-radius:9px;border:none;padding:6px 19px;font-size:1.01em;box-shadow:0 1px 6px #10b85930;cursor:pointer;transition:.12s;margin-bottom:3px;}
.action-btn:hover {background:#13c16a;}
@media(max-width:700px){.reset-wrap{padding:5px;}.resets-table th,.resets-table td{padding:6px 3px;font-size:.91em;}}
</style>
</head>
<body>

<input type="url" value="http://localhost/assessment/pages/reset_password.php?token=" style="width: 500px; border-radius: 10px; border: none; padding: 10px; background: #080a; color: white;">
<div class="reset-wrap">
  <div class="reset-head"><i class="fa fa-key"></i> Password Reset Requests</div>
  <?php if(isset($_GET['status'])): ?>
    <div style="background:#eaffdd;border:1.6px solid #10b85982;padding:11px 16px;border-radius:9px;color:#108c43;font-weight:700;margin-bottom:15px;">Update successful!</div>
  <?php endif; ?>
  <form method="get" style="margin-bottom:15px;display:flex;gap:7px;">
    <input type="text" name="q" placeholder="Search by email..." value="<?php echo htmlspecialchars($filter);?>" style="padding:8px 10px;border-radius:8px;border:1.2px solid #10b85977;" />
    <button style="background:#10b859;color:#fff;border:none;padding:8px 17px;border-radius:8px;font-weight:800;">Search</button>
  </form>
  <?php if(!$resets): ?>
    <div style="padding:37px 18px;text-align:center;color:#1ebc47;font-weight:700;font-size:1.11em;">No password reset requests found.</div>
  <?php else: ?>
  <table class="resets-table">
    <thead>
      <tr>
        <th>Email</th>
        <th>Token</th>
        <th>Expires</th>
        <th>Status</th>
        <th>Created</th>
        <th>Sent At</th>
        <th>Used At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($resets as $r):
        $status = $r['sent_status'];
        $is_expired = strtotime($r['expires']) < time() && $status != 'used';
      ?>
      <tr>
        <td><?php echo htmlspecialchars($r['email']); ?></td>
        <td style="word-break:break-all;max-width:160px;"><?php echo htmlspecialchars($r['token']); ?></td>
        <td><?php echo htmlspecialchars($r['expires']); ?></td>
        <td>
          <span class="reset-status-badge <?php echo $status; if($is_expired) echo ' expired'; ?>">
            <?php echo $is_expired ? 'expired' : $status; ?>
          </span>
        </td>
        <td><?php echo htmlspecialchars($r['created_at']); ?></td>
        <td><?php echo htmlspecialchars($r['sent_at']); ?></td>
        <td><?php echo htmlspecialchars($r['used_at']); ?></td>
        <td>
          <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <?php if($status != 'used'): ?>
            <button name="set_used" value="1" class="action-btn" onclick="return confirm('Mark as used?');">Mark Used</button>
            <?php endif;?>
            <?php if($status != 'invalid' && !$is_expired): ?>
            <button name="invalidate" value="1" class="action-btn" onclick="return confirm('Invalidate this reset?');" style="background:#f24b4b;">Invalidate</button>
            <?php endif;?>
          </form>
        </td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <?php endif;?>
</div>
</body>
</html>