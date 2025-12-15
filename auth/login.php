<?php
session_start();
include("../config/db.php");

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare(
  "SELECT * FROM users WHERE username = ?"
);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {

  // Save session
  $_SESSION['user_id'] = $user['user_id'];
  $_SESSION['username'] = $user['username'];
  $_SESSION['role'] = $user['role'];

  // Redirect based on role
  if ($user['role'] === 'ADMIN') {
    header("Location: ../admin/dashboard.php");
  } else {
    header("Location: ../user/dashboard.php");
  }
  exit;

} else {
  echo "❌ Invalid Username or Password";
}

