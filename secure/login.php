<?php
require 'secure_session.php';
require 'db_connection.php';
require 'security_log.php';
require 'csrf.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        security_log("CSRF FAILURE on login");
        http_response_code(400);
        exit('Invalid request.');
    }
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        security_log("LOGIN SUCCESS for username='{$username}', user_id={$user['id']}");

        header("Location: dashboard.php");
        exit;
    } else {
        security_log("LOGIN FAILURE for username='{$username}'");
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            margin:0;
        }
        .login-container {
            background:#fff;
            padding:20px 30px;
            border-radius:8px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
            width:300px;
        }
        h2 { text-align:center; margin-bottom:20px; }
        .form-group { margin-bottom:15px; }
        label { display:block; margin-bottom:5px; }
        input[type="text"], input[type="password"] {
            width:100%;
            padding:8px;
            box-sizing:border-box;
        }
        button {
            width:100%;
            padding:8px;
            border:none;
            background:#007bff;
            color:#fff;
            cursor:pointer;
        }
        .error {
            color:red;
            font-size:14px;
            margin-bottom:10px;
            text-align:center;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required />
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required />
        </div>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
