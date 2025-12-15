<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}

/* Fetch quizzes */
$quizzes = $conn->query("SELECT quiz_id, title FROM quizzes");

$success = "";
$error = "";

/* Handle form submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $quiz_id   = (int)$_POST['quiz_id'];
  $round_id  = (int)$_POST['round_id'];

  $question  = trim($_POST['question_text']);
  $a         = trim($_POST['option_a']);
  $b         = trim($_POST['option_b']);
  $c         = trim($_POST['option_c']);
  $d         = trim($_POST['option_d']);
  $correct   = $_POST['correct_option'];
  $explain   = trim($_POST['explanation']);

  /* ✅ Verify round belongs to quiz (IMPORTANT) */
  $check = $conn->prepare(
    "SELECT round_id FROM rounds WHERE round_id=? AND quiz_id=?"
  );
  $check->bind_param("ii", $round_id, $quiz_id);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows !== 1) {
    $error = "Invalid round selected for this quiz.";
  } else {

    $stmt = $conn->prepare(
      "INSERT INTO questions
      (round_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
      "isssssss",
      $round_id, $question, $a, $b, $c, $d, $correct, $explain
    );

    if ($stmt->execute()) {
      $success = "Question added successfully.";
    } else {
      $error = "Failed to add question.";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Question</title>
  <link rel="stylesheet" href="../css/admin.css">

</head>
<body>

<h2>Add Question</h2>
<a href="dashboard.php">⬅ Back</a>
<hr>

<?php if ($success): ?>
  <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
  <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

  <label>Quiz:</label><br>
  <select id="quizSelect" name="quiz_id" required>
    <option value="">Select Quiz</option>
    <?php while ($q = $quizzes->fetch_assoc()): ?>
      <option value="<?= $q['quiz_id'] ?>">
        <?= htmlspecialchars($q['title']) ?>
      </option>
    <?php endwhile; ?>
  </select>
  <br><br>

  <label>Round:</label><br>
  <select name="round_id" id="roundSelect" required>
    <option value="">Select Round</option>
  </select>
  <br><br>

  <label>Question:</label><br>
  <textarea name="question_text" required></textarea><br><br>

  <input name="option_a" placeholder="Option A" required><br>
  <input name="option_b" placeholder="Option B" required><br>
  <input name="option_c" placeholder="Option C" required><br>
  <input name="option_d" placeholder="Option D" required><br><br>

  <label>Correct Option:</label>
  <select name="correct_option" required>
    <option value="A">A</option>
    <option value="B">B</option>
    <option value="C">C</option>
    <option value="D">D</option>
  </select>
  <br><br>

  <textarea name="explanation" placeholder="Explanation (optional)"></textarea><br><br>

  <button type="submit">Add Question</button>
</form>

<script>
document.getElementById("quizSelect").addEventListener("change", function () {
  const quizId = this.value;
  const roundSelect = document.getElementById("roundSelect");

  if (!quizId) {
    roundSelect.innerHTML = "<option value=''>Select Round</option>";
    return;
  }

  roundSelect.innerHTML = "<option>Loading...</option>";

  fetch("fetch-rounds.php?quiz_id=" + quizId)
    .then(res => res.json())
    .then(data => {
      if (data.length === 0) {
        roundSelect.innerHTML =
          "<option value=''>No rounds available</option>";
        return;
      }

      roundSelect.innerHTML =
        "<option value=''>Select Round</option>";

      data.forEach(r => {
        roundSelect.innerHTML +=
          `<option value="${r.round_id}">${r.round_title}</option>`;
      });
    })
    .catch(() => {
      roundSelect.innerHTML =
        "<option value=''>Error loading rounds</option>";
    });
});
</script>

</body>
</html>
