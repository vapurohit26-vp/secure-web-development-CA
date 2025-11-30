<?php

?>
<div class="sidebar">
    <h2>Task Manager</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></p>
    <ul>
        <li><a href="dashboard.php">Tasks</a></li>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li><a href="add_user.php">Add User</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<style>
    .sidebar {
        width:220px;
        background:#343a40;
        color:#fff;
        height:100vh;
        padding:20px;
        position:fixed;
        top:0;
        left:0;
    }
    .sidebar h2 { margin-top:0; }
    .sidebar ul { list-style:none; padding:0; margin-top:20px; }
    .sidebar li { margin-bottom:10px; }
    .sidebar a {
        color:#fff;
        text-decoration:none;
    }
    .sidebar a:hover { text-decoration:underline; }
</style>
