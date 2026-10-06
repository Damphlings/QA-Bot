<?php
session_start();

$loginError = '';
$validUsername = 'admin';
$validPassword = 'Admin@123';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_dashboard.php');
        exit;
    }

    $loginError = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Bot Admin Login</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
        body{min-height:100vh;display:flex;justify-content:center;align-items:center;background:#FBF5E6;color:#4a3b2a;}
        .login-shell{width:100%;max-width:420px;padding:36px 30px;border-radius:24px;background:rgba(255,251,240,.95);border:1px solid rgba(244,165,130,.18);box-shadow:0 20px 60px rgba(120,100,70,.25);}
        .login-shell h1{font-size:28px;margin-bottom:10px;color:#4a3b2a;}
        .login-shell p{color:#7a6a52;margin-bottom:28px;}
        .login-shell label{display:block;color:#7a6a52;font-size:14px;margin-bottom:8px;}
        .login-shell input{width:100%;padding:14px 16px;margin-bottom:18px;border:none;border-radius:14px;background:#F1E7CF;color:#4a3b2a;outline:none;font-size:15px;}
        .login-shell button{width:100%;padding:14px 16px;border:none;border-radius:14px;background:#F4A582;color:#5a3a22;font-size:16px;font-weight:600;cursor:pointer;transition:.2s, transform .2s;box-shadow:0 12px 30px rgba(244,165,130,.2);}
        .login-shell button:hover{background:#E8916A;transform:translateY(-1px);}
        .login-shell .error{margin-bottom:18px;padding:12px 14px;background:rgba(220,38,38,.16);color:#8a3b3b;border:1px solid rgba(248,113,113,.3);border-radius:12px;}
        .login-shell .note{margin-top:14px;font-size:13px;color:#7a6a52;}
    </style>
</head>
<body>
    <div class="login-shell">
        <h1>QA Bot Admin Login</h1>
        <p>Enter your admin credentials to access the dashboard.</p>
        <?php if ($loginError): ?>
            <div class="error"><?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" placeholder="admin" required>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••" required>
            <button type="submit">Sign In</button>
        </form>
        <p class="note">Default admin credentials are <strong>admin / Admin@123</strong>. Change them in <code>admin_login.php</code> after install.</p>
    </div>
</body>
</html>
