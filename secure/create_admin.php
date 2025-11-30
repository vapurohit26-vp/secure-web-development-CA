<?php
require 'db_connection.php';

$full_name = "Admin User";
$username = "admin";
$password = password_hash("123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$full_name, $username, $password, $role]);

echo "New admin created!";
