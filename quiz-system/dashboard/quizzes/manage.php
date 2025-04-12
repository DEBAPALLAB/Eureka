<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.html");
    exit;
}

$query = "SELECT * FROM quizzes ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Quizzes</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f6f9;
      margin: 0;
    }

    .content-wrapper {
      margin-left: 220px;
      padding: 2rem;
      transition: margin-left 0.3s ease;
    }

    .sidebar.minimized + .content-wrapper {
      margin-left: 70px;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 1.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      border-radius: 12px;
      overflow: hidden;
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #4e73df;
      color: white;
    }

    tr:hover {
      background: #f0f4ff;
    }

    .actions a {
      padding: 6px 10px;
      margin-right: 6px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .edit {
      background-color: #1cc88a;
      color: white;
    }

    .delete {
      background-color: #e74a3b;
      color: white;
    }

    .add-btn {
      display: inline-block;
      background-color: #4e73df;
      color: white;
      padding: 10px 16px;
      border-radius: 8px;
      margin-bottom: 1rem;
      text-decoration: none;
    }

    /* Handle sidebar toggle layout shift */
    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 70px;
      }
    }
  </style>
</head>
<body>

  <?php include '../navbar.php'; ?>

  <div class="content-wrapper">
    <h2>Manage Quizzes</h2>
    <a href="create.php" class="add-btn">+ Create New Quiz</a>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Topic</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($quiz = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $quiz['id'] ?></td>
            <td><?= htmlspecialchars($quiz['title']) ?></td>
            <td><?= htmlspecialchars($quiz['topic']) ?></td>
            <td><?= $quiz['created_at'] ?></td>
            <td class="actions">
              <a href="edit.php?id=<?= $quiz['id'] ?>" class="edit">Edit</a>
              <a href="delete.php?id=<?= $quiz['id'] ?>" class="delete" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
