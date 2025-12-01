<?php
require 'auth_check.php';
require 'db_connection.php';

$message = "";


if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name    = $_POST['full_name'] ?? '';
    $username     = $_POST['username'] ?? '';
    $raw_password = $_POST['password'] ?? '';
    
    $role         = $_POST['role'] ?? 'employee';

    if ($full_name && $username && $raw_password) {
        
        $password = $raw_password;

        
        $checkSql = "SELECT id FROM users WHERE username = '$username'";
        $res = $conn->query($checkSql);

        if ($res && $res->rowCount() > 0) {
            $message = "Username already taken.";
        } else {
            
            $insertSql = "
                INSERT INTO users (full_name, username, password, role)
                VALUES ('$full_name', '$username', '$password', '$role')";
            $conn->exec($insertSql);

            $message = "User created successfully.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User - Task Manager</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; }
        .content { margin-left:240px; padding:20px; }
        .form-card { background:#f9f9f9; padding:15px; border-radius:6px; max-width:400px; }
        .form-group { margin-bottom:10px; }
        label { display:block; margin-bottom:5px; }
        input[type="text"], input[type="password"], select {
            width:100%; padding:6px; box-sizing:border-box;
        }
        button {
            padding:8px 12px; border:none; background:#007bff; color:#fff; cursor:pointer;
        }
        .msg { margin-bottom:10px; color:green; }
        .error { color:red; }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Add User</h1>
    <div class="form-card">
        <?php if ($message): ?>
            
            <div class="<?= strpos($message, 'success') !== false ? 'msg' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="add_user.php">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="employee">Employee</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit">Create User</button>
        </form>
    </div>
</div>
</body>
</html>
