<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.html");
  exit;
}

include("../config/db.php");

/* 1️⃣ Validate round_id */
if (!isset($_GET['round_id'])) {
  die("Round not specified");
}

$round_id = (int)$_GET['round_id'];

/* 2️⃣ Fetch round + quiz details (SAFE) */
$stmt = $conn->prepare(
  "SELECT r.round_id, r.round_title, r.time_limit, r.quiz_id, q.title
   FROM rounds r
   JOIN quizzes q ON r.quiz_id = q.quiz_id
   WHERE r.round_id = ?"
);
$stmt->bind_param("i", $round_id);
$stmt->execute();
$roundQuery = $stmt->get_result();

if ($roundQuery->num_rows === 0) {
  die("Invalid round");
}

$round = $roundQuery->fetch_assoc();
$quiz_id = (int)$round['quiz_id'];

/* 3️⃣ Fetch questions for this round (SAFE) */
$stmt = $conn->prepare(
  "SELECT question_text, option_a, option_b, option_c, option_d, correct_option, explanation
   FROM questions
   WHERE round_id = ?"
);
$stmt->bind_param("i", $round_id);
$stmt->execute();
$result = $stmt->get_result();

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
    "correct" => $row['option_' . strtolower($row['correct_option'])],
    "explanation" => $row['explanation']
  ];
}

if (count($questions) === 0) {
  die("No questions added for this round yet.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($round['title']) ?> - <?= htmlspecialchars($round['round_title']) ?></title>
  <link rel="stylesheet" href="../css/question.css">
</head>

<body>

<!-- Loader -->
<div id="loader">
  <div class="loader"></div>
</div>

<header>
  <h2><?= htmlspecialchars($round['title']) ?></h2>
  <p><?= htmlspecialchars($round['round_title']) ?></p>
</header>

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

<!-- 4️⃣ SEND DATA TO JS (IMPORTANT FIXES HERE) -->
<script>
  const questions = <?= json_encode($questions, JSON_UNESCAPED_UNICODE) ?>;
  const QUIZ_ID = <?= (int)$quiz_id ?>;
  const ROUND_ID = <?= (int)$round_id ?>;
  const TIME_LIMIT = <?= (int)$round['time_limit'] ?>;
</script>

<!-- 5️⃣ LOAD UPDATED JS -->
<script src="../js/script.js"></script>

</body>
</html>
