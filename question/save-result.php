<?php
session_start();
include("../config/db.php");

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["error" => "not_logged_in"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  echo json_encode(["error" => "invalid_json"]);
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$quiz_id = (int)($data['quiz_id'] ?? 0);
$total_score = (int)($data['score'] ?? 0);
$correct = (int)($data['correct'] ?? 0);
$wrong = (int)($data['wrong'] ?? 0);

if ($quiz_id === 0) {
  echo json_encode(["error" => "quiz_id_missing"]);
  exit;
}

/* Prevent duplicate attempt */
$check = $conn->prepare(
  "SELECT result_id FROM results WHERE user_id=? AND quiz_id=?"
);
$check->bind_param("ii", $user_id, $quiz_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
  echo json_encode(["status" => "already_saved"]);
  exit;
}

/* Insert result */
$stmt = $conn->prepare(
  "INSERT INTO results
   (user_id, quiz_id, total_score, correct_answers, wrong_answers)
   VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
  "iiiii",
  $user_id,
  $quiz_id,
  $total_score,
  $correct,
  $wrong
);

if ($stmt->execute()) {
  echo json_encode(["status" => "saved"]);
} else {
  echo json_encode([
    "error" => "db_failed",
    "msg" => $stmt->error
  ]);
}
