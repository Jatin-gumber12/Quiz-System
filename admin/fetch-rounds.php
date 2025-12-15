<?php
include("../config/db.php");

if (!isset($_GET['quiz_id'])) {
  echo json_encode([]);
  exit;
}

$quiz_id = (int)$_GET['quiz_id'];

$stmt = $conn->prepare(
  "SELECT round_id, round_title FROM rounds WHERE quiz_id = ?"
);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();

$result = $stmt->get_result();

$rounds = [];
while ($row = $result->fetch_assoc()) {
  $rounds[] = $row;
}

header("Content-Type: application/json");
echo json_encode($rounds);
