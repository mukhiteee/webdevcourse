<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course HQ – Coming Soon!</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0; padding: 0;
      min-height: 100vh;
      background: linear-gradient(110deg, #fafffe 60%, #e2feee 107%);
      font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .promo-card {
      max-width: 399px;
      width: 95vw;
      background: #fff;
      border-radius: 24px;
      box-shadow: 0 6px 30px #1eea8a17, 0 0px 7px #1eea8a34;
      padding: 33px 16px 22px 16px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .badge {
      background: linear-gradient(90deg,#04b159 68%,#23ffa1 95%);
      color: #fff;
      font-size: 1.15em;
      font-weight: 800;
      letter-spacing: 2px;
      border-radius: 14px 14px 14px 1px;
      padding: 8px 23px 7px;
      display: inline-block;
      box-shadow: 0 2px 8px #13e56e18;
      margin-bottom: 20px;
    }
    .main-title {
      font-size: 1.42em;
      font-weight: 900;
      color: #075933;
      display: flex; align-items: center; gap:12px; justify-content: center;
      letter-spacing: .4px;
      margin-bottom: 8px;
      margin-top: 6px;
    }
    .emoji {
      font-size: 1.4em;
      margin-bottom: -5px;
    }
    .hype {
      color: #10a759;
      background: #eafdeb;
      border-radius: 9px;
      display: inline-block;
      padding: 1.5px 9px 1.5px 10px;
      font-weight: 700;
      margin-bottom: 15px;
      font-size: 1.13em;
      margin-top: 7px;
      letter-spacing: .07em;
    }
    .msg {
      color: #184d34;
      font-size: 1.07em;
      margin: 20px 0 16px 0;
      line-height: 1.6;
      font-weight: 500;
    }
    .coming-links {
      margin: 25px 0 20px;
      display: flex; flex-direction: column; gap: 15px; align-items: stretch;
    }
    .coming-link {
      display: flex; align-items: center; gap: 10px; justify-content: center;
      background: #f2fff9;
      color: #13a05e;
      font-weight: 700;
      border-radius: 11px;
      font-size: 1.05em;
      padding: 13px 8px 11px;
      text-decoration: none;
      box-shadow: 0 1px 8px #0bea5e16;
      border: 2px solid #e1fbe8;
      transition: box-shadow 0.13s, background 0.17s, color 0.13s;
    }
    .coming-link:hover,.coming-link:active {
      background: #caffde;
      color: #085b2e;
      box-shadow: 0 2px 15px #13e56e22;
    }
    .coming-link i { font-size: 1.14em;}
    .footer-note {
      color: #6e8977;
      font-size: .97em;
      margin-top: 21px;
      line-height: 1.52;
      padding-top: 4px;
    }
    .blur-bg {
      position: absolute;
      width: 220px;
      height: 220px;
      top: -50px; left: -40px;
      background: radial-gradient(circle at 35% 55%, #50ffe0af 0%, #fff 70%);
      z-index: 0;
      filter: blur(14px);
      opacity: .65;
    }
    @media (max-width: 520px) {
      .promo-card { padding: 17px 4vw 13px 4vw; }
      .main-title { font-size: 1.18em; }
      .coming-link { font-size: 1.015em; }
    }
  </style>
</head>
<body>
  <div class="promo-card">
    <div class="blur-bg"></div>
    <div class="badge">NEW COHORT DROPPING SOON</div>
    <div class="main-title">
      <span class="emoji">🚀</span>
      Layout Mastery Bootcamp
    </div>
    <div class="hype">
      Get ready to build real, modern web layouts like a pro!
    </div>
    <div class="msg">
      Registration for this course isn’t open yet.<br>
      <b>Stay tuned for the launch date – all modules and projects will go live here soon.</b>
    </div>
    <div style="margin-bottom:6px; font-size:1em; color:#12735e;">
      Here’s what you can do in the meantime:
    </div>
    <div class="coming-links">
      <a href="https://youtube.com/@yourchannel" class="coming-link" target="_blank"><i class="fab fa-youtube"></i> Follow on YouTube for sneak peeks & live sessions</a>
      <a href="https://github.com/yourusername/layout-mastery" class="coming-link" target="_blank"><i class="fab fa-github"></i> Explore the project samples on GitHub</a>
      <a href="https://chat.whatsapp.com/invite/yourgroup" class="coming-link" target="_blank"><i class="fab fa-whatsapp"></i> Join our WhatsApp group for launch updates</a>
      <a href="mailto:hello@yourbrand.com" class="coming-link" target="_blank"><i class="fas fa-envelope"></i> Contact us for partnership or questions</a>
    </div>
    <div class="footer-note">
      Platform will become public to all registered users on launch day.<br>
      Don’t miss any updates—join the community and get notified first!
    </div>
  </div>
</body>
</html>