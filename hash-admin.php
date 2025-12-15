<?php
include("config/db.php");

$hashedPassword = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $conn->prepare(
  "UPDATE users SET password=? WHERE username='admin'"
);
$stmt->bind_param("s", $hashedPassword);
$stmt->execute();

echo "Admin password hashed successfully";
