<?php
session_start();
include("../config/db.php");

/* Auth check */
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../css/user.css">
</head>
<body>

<!-- HEADER -->
<div class="dashboard-header">
  <h2>User Dashboard – <?= htmlspecialchars($_SESSION['username']) ?></h2>
  <a href="../auth/logout.php">Logout</a>
</div>

<!-- MAIN CONTAINER -->
<div class="dashboard-container">

  <div class="section-title">Available Quizzes</div>

  <?php
  $res = $conn->query("SELECT quiz_id, title FROM quizzes WHERE is_active = 1");

  if ($res->num_rows === 0):
  ?>
    <p>No quizzes available right now.</p>
  <?php else: ?>
    <div class="quiz-list">
      <?php while ($q = $res->fetch_assoc()): ?>
        <div class="quiz-row">
          <span><?= htmlspecialchars($q['title']) ?></span>
          <a href="quiz-rounds.php?quiz_id=<?= (int)$q['quiz_id'] ?>">
            View Rounds
          </a>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

  <div class="dashboard-links">
    <a href="my-results.php">📊 My Results</a>
  </div>

</div>

</body>
</html>
