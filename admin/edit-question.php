<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}

$id = (int)$_GET['id'];

/* Handle update */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $stmt = $conn->prepare(
    "UPDATE questions SET
      round_id=?,
      question_text=?,
      option_a=?,
      option_b=?,
      option_c=?,
      option_d=?,
      correct_option=?,
      explanation=?
     WHERE question_id=?"
  );

  $stmt->bind_param(
    "isssssss i",
    $_POST['round_id'],
    $_POST['question_text'],
    $_POST['option_a'],
    $_POST['option_b'],
    $_POST['option_c'],
    $_POST['option_d'],
    $_POST['correct_option'],
    $_POST['explanation'],
    $id
  );

  $stmt->execute();
  header("Location: manage-questions.php");
  exit;
}

/* Fetch question */
$q = $conn->query(
  "SELECT * FROM questions WHERE question_id=$id"
)->fetch_assoc();

/* Fetch rounds for dropdown */
$rounds = $conn->query(
  "SELECT r.round_id, r.round_title, q.title AS quiz_title
   FROM rounds r
   JOIN quizzes q ON r.quiz_id = q.quiz_id"
);
?>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>

<h2>Edit Question</h2>
<a href="manage-questions.php">⬅ Back</a>
<hr>

<form method="POST">

  <label>Round:</label><br>
  <select name="round_id" required>
    <?php while ($r = $rounds->fetch_assoc()): ?>
      <option value="<?= $r['round_id'] ?>"
        <?= $r['round_id'] == $q['round_id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($r['quiz_title']) ?> → <?= htmlspecialchars($r['round_title']) ?>
      </option>
    <?php endwhile; ?>
  </select>
  <br><br>

  <label>Question:</label><br>
  <textarea name="question_text" required><?= htmlspecialchars($q['question_text']) ?></textarea><br><br>

  A: <input name="option_a" value="<?= htmlspecialchars($q['option_a']) ?>" required><br>
  B: <input name="option_b" value="<?= htmlspecialchars($q['option_b']) ?>" required><br>
  C: <input name="option_c" value="<?= htmlspecialchars($q['option_c']) ?>" required><br>
  D: <input name="option_d" value="<?= htmlspecialchars($q['option_d']) ?>" required><br><br>

  <label>Correct Option:</label>
  <select name="correct_option" required>
    <?php foreach (['A','B','C','D'] as $opt): ?>
      <option value="<?= $opt ?>" <?= $q['correct_option']===$opt?'selected':'' ?>>
        <?= $opt ?>
      </option>
    <?php endforeach; ?>
  </select>
  <br><br>

  <label>Explanation:</label><br>
  <textarea name="explanation"><?= htmlspecialchars($q['explanation']) ?></textarea><br><br>

  <button type="submit">Update Question</button>
</form>
