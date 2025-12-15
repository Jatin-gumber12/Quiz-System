<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}

/* Delete question */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  $stmt = $conn->prepare(
    "DELETE FROM questions WHERE question_id = ?"
  );
  $stmt->bind_param("i", $id);
  $stmt->execute();

  header("Location: manage-questions.php");
  exit;
}
?>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>

<h2>Manage Questions</h2>
<a href="dashboard.php">⬅ Back</a>
<hr>

<table border="1" cellpadding="8">
<tr>
  <th>ID</th>
  <th>Question</th>
  <th>Quiz</th>
  <th>Round</th>
  <th>Action</th>
</tr>

<?php
$result = $conn->query(
  "SELECT 
     q.question_id,
     q.question_text,
     r.round_title,
     z.title AS quiz_title
   FROM questions q
   JOIN rounds r ON q.round_id = r.round_id
   JOIN quizzes z ON r.quiz_id = z.quiz_id
   ORDER BY z.title, r.round_title"
);

while ($q = $result->fetch_assoc()):
?>
<tr>
  <td><?= $q['question_id'] ?></td>
  <td><?= htmlspecialchars($q['question_text']) ?></td>
  <td><?= htmlspecialchars($q['quiz_title']) ?></td>
  <td><?= htmlspecialchars($q['round_title']) ?></td>
  <td>
    <a href="edit-question.php?id=<?= $q['question_id'] ?>">Edit</a> |
    <a href="?delete=<?= $q['question_id'] ?>"
       onclick="return confirm('Delete this question?')">
       Delete
    </a>
  </td>
</tr>
<?php endwhile; ?>
</table>
