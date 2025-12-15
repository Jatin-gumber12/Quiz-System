<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
  header("Location: ../login.html");
  exit;
}

/* Delete user (prevent self-delete) */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  if ($id !== (int)$_SESSION['user_id']) {
    $stmt = $conn->prepare(
      "DELETE FROM users WHERE user_id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
  }

  header("Location: manage-users.php");
  exit;
}
?>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>

<h2>Manage Users</h2>
<a href="dashboard.php">⬅ Back</a>
<hr>

<table border="1" cellpadding="8">
<tr>
  <th>ID</th>
  <th>Username</th>
  <th>Email</th>
  <th>Role</th>
  <th>Action</th>
</tr>

<?php
$result = $conn->query(
  "SELECT user_id, username, email, role FROM users ORDER BY user_id ASC"
);

while ($u = $result->fetch_assoc()):
?>
<tr>
  <td><?= $u['user_id'] ?></td>
  <td><?= htmlspecialchars($u['username']) ?></td>
  <td><?= htmlspecialchars($u['email']) ?></td>
  <td><?= $u['role'] ?></td>
  <td>
    <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
      <a href="?delete=<?= $u['user_id'] ?>"
         onclick="return confirm('Delete this user?')">
         Delete
      </a>
    <?php else: ?>
      —
    <?php endif; ?>
  </td>
</tr>
<?php endwhile; ?>
</table>
