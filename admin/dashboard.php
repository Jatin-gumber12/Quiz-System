<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}
?>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>

<h2>Admin Dashboard</h2>
<hr>

<ul>
  <li><a href="manage-users.php">👤 Manage Users</a></li>
  <li><a href="manage-quizzes.php">🧠 Manage Quizzes</a></li>
  <li><a href="add-question.php">➕ Add Question (Round-wise)</a></li>
  <li><a href="manage-questions.php">❓ Manage Questions</a></li>
  <li><a href="view-results.php">📊 View Results</a></li>
  <li><a href="../auth/logout.php">🚪 Logout</a></li>
</ul>
