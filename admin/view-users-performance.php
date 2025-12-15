<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}
?>

<h2>User Performance</h2>
<a href="dashboard.php">⬅ Back</a>
<hr>

<table border="1" cellpadding="8">
<tr>
  <th>User</th>
  <th>Email</th>
  <th>Quiz</th>
  <th>Total Score</th>
  <th>Correct</th>
  <th>Wrong</th>
  <th>Date</th>
</tr>

<?php
$sql = "
SELECT 
  u.username,
  u.email,
  q.title AS quiz_title,
  r.total_score,
  r.correct_answers,
  r.wrong_answers,
  r.submitted_at
FROM results r
JOIN users u   ON r.user_id = u.user_id
JOIN quizzes q ON r.quiz_id = q.quiz_id
ORDER BY u.username, r.submitted_at DESC
";

$res = $conn->query($sql);

while ($row = $res->fetch_assoc()):
?>
<tr>
  <td><?= htmlspecialchars($row['username']) ?></td>
  <td><?= htmlspecialchars($row['email']) ?></td>
  <td><?= htmlspecialchars($row['quiz_title']) ?></td>
  <td><?= $row['total_score'] ?></td>
  <td><?= $row['correct_answers'] ?></td>
  <td><?= $row['wrong_answers'] ?></td>
  <td><?= $row['submitted_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>
