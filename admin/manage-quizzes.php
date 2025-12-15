<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}

/* Add new quiz */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);

  if ($title !== '') {
    $stmt = $conn->prepare(
      "INSERT INTO quizzes (title, is_active) VALUES (?, 1)"
    );
    $stmt->bind_param("s", $title);
    $stmt->execute();
  }

  header("Location: manage-quizzes.php");
  exit;
}

/* Delete quiz (CASCADE deletes rounds & questions) */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  $stmt = $conn->prepare(
    "DELETE FROM quizzes WHERE quiz_id = ?"
  );
  $stmt->bind_param("i", $id);
  $stmt->execute();

  header("Location: manage-quizzes.php");
  exit;
}
?>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>

<h2>Manage Quizzes</h2>
<a href="dashboard.php">⬅ Back to Admin Dashboard</a>

<hr>

<h3>Add New Quiz</h3>
<form method="POST">
  <label>Quiz Title:</label><br>
  <input name="title" placeholder="Math Quiz" required>
  <br><br>
  <button type="submit">Add Quiz</button>
</form>

<hr>

<h3>Existing Quizzes</h3>

<table border="1" cellpadding="8">
<tr>
  <th>ID</th>
  <th>Title</th>
  <th>Status</th>
  <th>Actions</th>
</tr>

<?php
$res = $conn->query(
  "SELECT quiz_id, title, is_active
   FROM quizzes
   ORDER BY quiz_id DESC"
);

while ($q = $res->fetch_assoc()):
?>
<tr>
  <td><?= $q['quiz_id'] ?></td>
  <td><?= htmlspecialchars($q['title']) ?></td>
  <td><?= $q['is_active'] ? 'Active' : 'Inactive' ?></td>
  <td>
    <a href="manage-rounds.php?quiz_id=<?= $q['quiz_id'] ?>">
      🧩 Manage Rounds
    </a>
    |
    <a href="?delete=<?= $q['quiz_id'] ?>"
       onclick="return confirm('Delete quiz and all its rounds & questions?')">
       🗑 Delete
    </a>
  </td>
</tr>
<?php endwhile; ?>
</table>
