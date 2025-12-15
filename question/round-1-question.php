<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.html");
  exit;
}
?>

<?php
// 1️⃣ DB connection
include("../config/db.php");

// 2️⃣ Select quiz id (Round 1)
$quiz_id = 1;

// 3️⃣ Fetch questions from database
$result = $conn->query(
  "SELECT * FROM questions WHERE quiz_id = $quiz_id"
);

$questions = [];

while ($row = $result->fetch_assoc()) {
  $questions[] = [
    "question" => $row['question_text'],
    "options" => [
      $row['option_a'],
      $row['option_b'],
      $row['option_c'],
      $row['option_d']
    ],
    "correct" =>
      $row['option_' . strtolower($row['correct_option'])],
    "explanation" => $row['explanation']
  ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Round 1 | Quiz</title>
  <link rel="stylesheet" href="../css/question.css">
</head>

<body>

<!-- Loader -->
<div id="loader">
  <div class="loader"></div>
</div>

<header>Welcome to the Quiz!</header>

<div class="quiz-container">

  <div class="progress-container">
    <div id="progress-bar"></div>
  </div>

  <div id="timer" class="hidden">
    Time Left: <span id="time-remaining"></span>
  </div>

  <button id="start-timer-btn" onclick="startTimer()">Start Timer</button>

  <div class="question-section">
    <h3 id="question-text">Loading...</h3>
    <ul class="options" id="options-list"></ul>
  </div>

  <div id="feedback" class="hidden"></div>
  <div id="explanation" class="hidden"></div>

  <button id="back-btn" class="hidden" onclick="loadPreviousQuestion()">
    Back
  </button>
  <button id="next-btn" class="hidden" onclick="loadNextQuestion()">
    Next
  </button>

</div>

<footer class="footer"></footer>

<!-- 4️⃣ SEND QUESTIONS TO JS -->
<script>
  const questions = <?php echo json_encode($questions); ?>;
</script>

<!-- 5️⃣ NEXT ROUND LINK -->
<script>
  const NEXT_ROUND_URL = "../start-page/round-2-start.html";
</script>

<!-- 6️⃣ LOAD YOUR EXISTING JS -->
<script src="../js/script.js"></script>

</body>
</html>
