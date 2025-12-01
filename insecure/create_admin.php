<?php
require 'db_connection.php';

$full_name = "Admin User";
$username  = "admin";
$password  = "Myname@2010";  
$role      = "admin";


$sql = "INSERT INTO users (full_name, username, password, role)
        VALUES ('$full_name', '$username', '$password', '$role')";
$conn->exec($sql);

echo "New admin created!";
