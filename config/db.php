<?php
$conn = new mysqli("localhost", "root", "", "quiz_app");

if ($conn->connect_error) {
    die("Database Connection Failed");
}
?>
