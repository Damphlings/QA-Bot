<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Bot Admin Dashboard</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
        body{min-height:100vh;background:#FBF5E6;color:#4a3b2a;display:flex;justify-content:center;align-items:center;padding:20px;}
        .message-shell{width:100%;max-width:560px;padding:42px 34px;border-radius:28px;background:rgba(255,251,240,.96);border:1px solid rgba(244,165,130,.18);box-shadow:0 35px 80px rgba(120,100,70,.25);}
        .message-shell h1{font-size:30px;margin-bottom:18px;color:#4a3b2a;}
        .message-shell p{color:#7a6a52;margin-bottom:28px;line-height:1.6;}
        .message-shell a{display:inline-flex;align-items:center;padding:14px 24px;border-radius:14px;background:#F4A582;color:#5a3a22;text-decoration:none;font-weight:700;transition:.2s;}
        .message-shell a:hover{background:#E8916A;}
        .message-shell .hint{margin-top:22px;font-size:14px;color:#7a6a52;}
    </style>
</head>
<body>
    <div class="message-shell">
        <h1>Admin Access Granted</h1>
        <p>You are signed in as QA Bot administrator. Use the button below to open the dashboard.</p>
        <a href="admin.html">Go to Admin Panel</a>
        <p class="hint">If you need to log out, use <code>logout.php</code> or close this browser tab.</p>
    </div>
</body>
</html>
