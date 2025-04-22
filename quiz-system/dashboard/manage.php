<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.html");
    exit;
}

$role = $_SESSION['role'];

$query = "SELECT * FROM quizzes ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Quizzes</title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Rubik', sans-serif;
      background: url('white.jpeg') no-repeat center center fixed;
      background-size: cover;
      color: #333;
      min-height: 100vh;
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
      color: orange; 
      margin-bottom: 1.5rem;
      font-size: 2rem;
      font-weight: bold;
      background-color: rgba(0, 0, 0, 0.75);
      padding: 1rem;
      border-radius: 12px;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .add-btn, .back-btn {
      background-color: orange;
      color: #000;
      padding: 10px 16px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .add-btn:hover {
      background-color: #e69500; 
    }

    .back-btn {
      background-color: #6c757d;
      color: #fff;
    }

    .back-btn:hover {
      background-color: #5a6268;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      background: rgba(0, 0, 0, 0.75);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
      color: #f2f2f2;
    }

    th {
      background-color: orange; 
      color: #000;
    }

    tr:hover {
      background-color:rgb(191, 173, 144);
    }

    .actions a {
      padding: 6px 12px;
      margin-right: 6px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 600;
    }

    .edit {
      background-color: #1cc88a;
      color: #f2f2f2;
    }

    .delete {
      background-color: #e74a3b;
      color: #f2f2f2;
    }

    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 70px;
      }

      .top-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .add-btn, .back-btn {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <div class="content-wrapper">
    <h2>Manage Quizzes</h2>

    <div class="top-bar">
      <a href="create.php" class="add-btn">+ Create New Quiz</a>
      <a href="index.php" class="back-btn">← Go back to Dashboard</a>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Topic</th>
          <th>Quiz Code</th>
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
            <td><?= htmlspecialchars($quiz['quiz_code']) ?></td>
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
