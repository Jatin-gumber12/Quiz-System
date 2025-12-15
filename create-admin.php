<?php
include("config/db.php");

$username = "admin";
$email = "admin@quiz.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "ADMIN";

$stmt = $conn->prepare(
  "INSERT INTO users (username, email, password, role)
   VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("ssss", $username, $email, $password, $role);
$stmt->execute();

echo "Admin created successfully";
