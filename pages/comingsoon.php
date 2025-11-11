<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Web Development for Beginners – Coming Soon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(110deg,#fafffe 60%,#e2feee 109%);
      font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
      min-height:100vh; margin:0; display:flex;justify-content:center;align-items:center;
    }
    .clean-card {
      width:96vw; max-width:400px;
      background:#fff; border-radius:19px;
      box-shadow:0 4px 21px #15dfa83a,0 0 7px #10b85922;
      padding:27px 14px 19px 14px;
      text-align:center; position:relative; overflow:hidden;
    }
    .main-title {
      font-size:1.35em;font-weight:800;color:#0cab6f; letter-spacing:.05em;
      margin-bottom:8px; margin-top:3px;
    }
    .status-tag {
      background:#0cb864; color:#fff; font-weight:800;margin-bottom:16px;
      border-radius:8px;padding:6px 21px 5px;display:inline-block;font-size:1em;letter-spacing:1px;
    }
    .short-note {
      font-size:1.08em;color:#155d42;font-weight:600;margin:19px 0 20px 0;
    }
    .cta-list {display:flex;flex-direction:column;gap:14px;margin:11px 0 16px;}
    .cta-link {
      display:flex;align-items:center;gap:10px;justify-content:center;
      background:#f4fff8;color:#10c25d;font-weight:700;
      border-radius:9px;font-size:1.06em;padding:12px 5px 10px;
      text-decoration:none;border:2px solid #dffbe3; box-shadow:0 1px 8px #0bea5e07;
      transition: box-shadow .13s, background .15s, color .13s;
    }
    .cta-link:hover{background:#cbffe9;color:#045c30;box-shadow:0 2px 15px #0abe6a24;}
    .cta-link i {font-size:1.15em;}
    .post-note {
      margin-top:15px;color:#198a4a;font-size:1.1em;font-weight:600;
      background:#e7fff0;border-radius:8px;padding:7px 9px 6px 9px;letter-spacing:.04em;
    }
    @media (max-width: 480px) {
      .clean-card { padding:14px 2vw 12px 2vw;}
    }
  </style>
</head>
<body>
  <div class="clean-card">
    <div class="main-title"><i class="fas fa-graduation-cap"></i> Web Development for Beginners</div>
    <div class="status-tag">Platform Launching Soon</div>
    <div class="short-note">
      The platform will be fully functional when the course starts.<br>
      Use the links below to stay connected and get updates.
    </div>
    <div class="cta-list">
      <a href="https://youtube.com/@yourchannel" class="cta-link" target="_blank">
        <i class="fab fa-youtube"></i> Subscribe to YouTube Channel
      </a>
      <a href="https://github.com/yourusername/yourrepo" class="cta-link" target="_blank">
        <i class="fab fa-github"></i> View Practicals on GitHub
      </a>
      <a href="https://chat.whatsapp.com/invite/yourgroup" class="cta-link" target="_blank">
        <i class="fab fa-whatsapp"></i> Join WhatsApp Community
      </a>
      <a href="https://wa.me/2348012345678" class="cta-link" target="_blank">
        <i class="fas fa-user-circle"></i> Contact Admin (WhatsApp)
      </a>
    </div>
    <div class="post-note">
      Join the WhatsApp community to stay updated – don’t miss the launch!
    </div>
  </div>
</body>
</html>