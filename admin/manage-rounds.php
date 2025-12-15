<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}

if (!isset($_GET['quiz_id'])) {
  die("Quiz not specified");
}

$quiz_id = (int)$_GET['quiz_id'];

/* Fetch quiz */
$stmt = $conn->prepare(
  "SELECT title FROM quizzes WHERE quiz_id = ?"
);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
  die("Quiz not found");
}

/* Add round */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $round_title = trim($_POST['round_title']);
  $time_limit  = (int)$_POST['time_limit'];

  if ($round_title !== '' && $time_limit > 0) {
    $stmt = $conn->prepare(
      "INSERT INTO rounds (quiz_id, round_title, time_limit)
       VALUES (?, ?, ?)"
    );
    $stmt->bind_param("isi", $quiz_id, $round_title, $time_limit);
    $stmt->execute();
  }

  header("Location: manage-rounds.php?quiz_id=$quiz_id");
  exit;
}

/* Fetch rounds */
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
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>

<h2>Manage Rounds – <?= htmlspecialchars($quiz['title']) ?></h2>
<a href="manage-quizzes.php">⬅ Back to Quizzes</a>
<hr>

<h3>Add New Round</h3>
<form method="post">
  <input type="text" name="round_title" placeholder="Round Title" required>
  <input type="number" name="time_limit" placeholder="Time Limit (sec)" min="1" required>
  <button type="submit">Add Round</button>
</form>

<hr>

<h3>Existing Rounds</h3>

<?php if ($rounds->num_rows === 0): ?>
  <p>No rounds added yet.</p>
<?php else: ?>
  <ul>
    <?php while ($r = $rounds->fetch_assoc()): ?>
      <li>
        <?= htmlspecialchars($r['round_title']) ?>
        (<?= $r['time_limit'] ?> sec)
      </li>
    <?php endwhile; ?>
  </ul>
<?php endif; ?>
