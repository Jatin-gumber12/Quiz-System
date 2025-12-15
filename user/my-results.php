<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.html");
  exit;
}

$user_id = (int)$_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Results</title>
  <link rel="stylesheet" href="../css/user.css">
</head>
<body>

<!-- HEADER -->
<div class="dashboard-header">
  <h2>My Quiz Results</h2>
  <a href="dashboard.php">⬅ Back</a>
</div>

<!-- CONTENT -->
<div class="results-container">

<table class="results-table">
<tr>
  <th>Quiz</th>
  <th>Total Score</th>
  <th>Correct</th>
  <th>Wrong</th>
  <th>Date</th>
</tr>

<?php
$total = 0;

$stmt = $conn->prepare(
  "SELECT q.title, r.total_score, r.correct_answers, r.wrong_answers, r.submitted_at
   FROM results r
   JOIN quizzes q ON r.quiz_id = q.quiz_id
   WHERE r.user_id = ?
   ORDER BY r.submitted_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo "<tr><td colspan='5'>No attempts yet.</td></tr>";
}

while ($row = $res->fetch_assoc()) {
  $total += $row['total_score'];
  echo "<tr>
    <td>".htmlspecialchars($row['title'])."</td>
    <td>{$row['total_score']}</td>
    <td>{$row['correct_answers']}</td>
    <td>{$row['wrong_answers']}</td>
    <td>{$row['submitted_at']}</td>
  </tr>";
}
?>

<tr class="total-row">
  <th>Total</th>
  <th colspan="4"><?= $total ?></th>
</tr>
</table>

</div>

</body>
</html>
