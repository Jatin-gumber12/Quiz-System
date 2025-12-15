<?php
include("../config/db.php");

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare(
  "INSERT INTO users (username, email, password, role)
   VALUES (?, ?, ?, 'USER')"
);

$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
  header("Location: ../login.html");
} else {
  echo "❌ User already exists";
}

