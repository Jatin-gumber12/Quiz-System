<?php
session_start();
include("../config/db.php");

/* Auth check */
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.html");
  exit;
}

/* Validate quiz_id */
if (!isset($_GET['quiz_id'])) {
  die("Quiz not specified");
}

$quiz_id = (int)$_GET['quiz_id'];

/* Fetch quiz safely */
$stmt = $conn->prepare(
  "SELECT title FROM quizzes WHERE quiz_id = ? AND is_active = 1"
);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quizRes = $stmt->get_result();

if ($quizRes->num_rows === 0) {
  die("Invalid or inactive quiz");
}

$quiz = $quizRes->fetch_assoc();

/* Fetch rounds safely */
$stmt = $conn->prepare(
  "SELECT round_id, round_title, time_limit
   FROM rounds
   WHERE quiz_id = ?
   ORDER BY round_id ASC"
);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$rounds = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($quiz['title']) ?> – Rounds</title>
  <link rel="stylesheet" href="../css/startpage.css">
  
</head>
<body>

<h2><?= htmlspecialchars($quiz['title']) ?></h2>
<h3>Select a Round</h3>

<?php if ($rounds->num_rows === 0): ?>
  <p>No rounds available for this quiz.</p>
<?php else: ?>
  <ul>
    <?php while ($r = $rounds->fetch_assoc()): ?>
      <li>
        <a href="../question/round.php?round_id=<?= (int)$r['round_id'] ?>">
          <?= htmlspecialchars($r['round_title']) ?>
          (<?= (int)$r['time_limit'] ?> sec)
        </a>
      </li>
    <?php endwhile; ?>
  </ul>
<?php endif; ?>

<hr>
<a href="dashboard.php">⬅ Back to Dashboard</a>

</body>
</html>
