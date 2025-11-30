<?php
require 'auth_check.php';
require 'db_connection.php';
require 'security_log.php';
require 'csrf.php';

$currentUserId = $_SESSION['user_id'];
$currentRole   = $_SESSION['user_role'] ?? 'employee';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        security_log("CSRF FAILURE on task POST by user_id={$currentUserId}");
        http_response_code(400);
        exit('Invalid request.');
    }
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
    $due_date    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $status      = $_POST['status'] ?? 'pending';

    if ($currentRole !== 'admin') {

        $assigned_to = $currentUserId;
    }

    if (!empty($_POST['task_id'])) {

        $task_id = (int)$_POST['task_id'];

        if ($currentRole !== 'admin') {
            $check = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND assigned_to = ?");
            $check->execute([$task_id, $currentUserId]);
            if ($check->rowCount() === 0) {
                http_response_code(403);
                exit('Unauthorized action.');
            }
        }

        $stmt = $conn->prepare(
            "UPDATE tasks SET title = ?, description = ?, assigned_to = ?, due_date = ?, status = ? WHERE id = ?"
        );
        $stmt->execute([$title, $description, $assigned_to, $due_date, $status, $task_id]);

        security_log("TASK UPDATE by user_id={$currentUserId}, task_id={$task_id}, status='{$status}'");
    } else {
      
        $stmt = $conn->prepare(
            "INSERT INTO tasks (title, description, assigned_to, due_date, status) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $description, $assigned_to, $due_date, $status]);

        $newTaskId = $conn->lastInsertId();
        security_log("TASK CREATE by user_id={$currentUserId}, task_id={$newTaskId}, status='{$status}'");
    }

    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task'])) {

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        security_log("CSRF FAILURE on task DELETE by user_id={$currentUserId}");
        http_response_code(400);
        exit('Invalid request.');
    }

    $task_id = (int)$_POST['delete_task'];

    if ($currentRole !== 'admin') {
        $check = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND assigned_to = ?");
        $check->execute([$task_id, $currentUserId]);
        if ($check->rowCount() === 0) {
            http_response_code(403);
            exit('Unauthorized action.');
        }
    }

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);

    security_log("TASK DELETE by user_id={$currentUserId}, task_id={$task_id}");

    header("Location: dashboard.php");
    exit;
}


$editing_task = null;
if (isset($_GET['edit'])) {
    $task_id = (int)$_GET['edit'];

    if ($currentRole === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND assigned_to = ?");
        $stmt->execute([$task_id, $currentUserId]);
    }

    $editing_task = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editing_task) {
        http_response_code(403);
        exit('Unauthorized action.');
    }
}

if ($currentRole === 'admin') {
    $tasks_stmt = $conn->query(
        "SELECT t.*, u.full_name AS assigned_name
         FROM tasks t
         LEFT JOIN users u ON t.assigned_to = u.id
         ORDER BY t.created_at DESC"
    );
    $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $tasks_stmt = $conn->prepare(
        "SELECT t.*, u.full_name AS assigned_name
         FROM tasks t
         LEFT JOIN users u ON t.assigned_to = u.id
         WHERE t.assigned_to = ?
         ORDER BY t.created_at DESC"
    );
    $tasks_stmt->execute([$currentUserId]);
    $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
}

$users_stmt = $conn->query("SELECT id, full_name FROM users ORDER BY full_name");
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager - Dashboard</title>
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
        }
        .content {
            margin-left:240px; 
            padding:20px;
        }
        table {
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }
        table, th, td {
            border:1px solid #ccc;
        }
        th, td {
            padding:8px;
            text-align:left;
        }
        .form-card {
            background:#f9f9f9;
            padding:15px;
            border-radius:6px;
        }
        .form-group { margin-bottom:10px; }
        label { display:block; margin-bottom:5px; }
        input[type="text"], textarea, select, input[type="date"] {
            width:100%;
            padding:6px;
            box-sizing:border-box;
        }
        button {
            padding:8px 12px;
            border:none;
            background:#28a745;
            color:#fff;
            cursor:pointer;
        }
        .btn-delete {
            background:#dc3545;
        }
        .btn-edit {
            background:#007bff;
        }
        a.btn-edit, a.btn-delete {
            color:#fff;
            text-decoration:none;
            padding:4px 8px;
            border-radius:4px;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h1>Tasks</h1>

    <div class="form-card">
        <h3><?php echo $editing_task ? 'Edit Task' : 'Add New Task'; ?></h3>
        <form method="POST" action="dashboard.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
            <?php if ($editing_task) { ?>
                
                <input type="hidden" name="task_id" value="<?php echo (int)$editing_task['id']; ?>">
            <?php } ?>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required
                       value="<?php echo htmlspecialchars($editing_task['title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?php 
                    echo htmlspecialchars($editing_task['description'] ?? ''); 
                ?></textarea>
            </div>
            <div class="form-group">
                <label>Assigned To</label>
                <select name="assigned_to">
                    <option value="">-- None --</option>
                    <?php foreach ($users as $u) { ?>
                        <option value="<?php echo $u['id']; ?>"
                            <?php 
                            if (isset($editing_task['assigned_to']) && $editing_task['assigned_to'] == $u['id']) {
                                echo 'selected';
                            }
                            ?>>
                            <?php echo htmlspecialchars($u['full_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date"
                       value="<?php echo htmlspecialchars($editing_task['due_date'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <?php $status_val = $editing_task['status'] ?? 'pending'; ?>
                <select name="status">
                    <option value="pending"     <?php echo $status_val === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $status_val === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed"   <?php echo $status_val === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            <button type="submit"><?php echo $editing_task ? 'Update Task' : 'Create Task'; ?></button>
        </form>
    </div>

    <h3>Task List</h3>
    <table>
        <thead>
        <tr>
            <th>id</th> 
            <th>Title</th>
            <th>Assigned To</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($tasks) === 0) { ?>
            <tr><td colspan="7">No tasks found.</td></tr>
        <?php } else { ?>
            <?php $i = 1; ?>
            <?php foreach ($tasks as $t) { ?>
                <tr>
                    <td><?php echo $i++; ?></td> 
                    <td><?php echo htmlspecialchars($t['title']); ?></td>
                    <td><?php echo htmlspecialchars($t['assigned_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($t['due_date']); ?></td>
                    <td><?php echo htmlspecialchars($t['status']); ?></td>
                    <td><?php echo htmlspecialchars($t['created_at']); ?></td>
                    <td>
                        <a class="btn-edit" href="dashboard.php?edit=<?php echo (int)$t['id']; ?>">Edit</a>
                            

    <form method="POST" action="dashboard.php" style="display:inline;"
          onsubmit="return confirm('Delete this task?');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
        <input type="hidden" name="delete_task" value="<?php echo (int)$t['id']; ?>">
        <button type="submit" class="btn-delete">Delete</button>
    </form>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
