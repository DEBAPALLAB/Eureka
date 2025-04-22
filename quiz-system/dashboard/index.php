<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.html");
    exit;
}

$name = htmlspecialchars($_SESSION['name']);
$role = htmlspecialchars($_SESSION['role']);
$join_error = $_SESSION['join_error'] ?? null;
unset($_SESSION['join_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: url('white.jpeg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Poppins', sans-serif;
      color: #fff;
    }

    .content-wrapper {
      margin-left: 220px;
      transition: margin-left 0.3s ease;
    }

    .content {
      width: 400px;
      margin: 5rem auto;
      padding: 2.5rem;
      background: rgba(0, 0, 0, 0.85);
      border-radius: 16px;
      box-shadow: 0 0 20px rgba(255, 165, 0, 0.4);
      text-align: center;
    }

    .content h1 {
      font-size: 28px;
      margin-bottom: 1rem;
      text-align: center;
    }

    .content p {
      margin-bottom: 1rem;
      color: #ccc;
      text-align: center;
    }

    .btn-action {
      display: inline-block;
      margin: 20px 10px 0 0;
      padding: 10px 20px;
      background-color: orange;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      transition: background 0.3s ease;
      font-weight: 600;
      border: none;
      cursor: pointer;
    }

    .btn-action:hover {
      background-color: #e69500;
    }

    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      padding: 12px 16px;
      margin: 20px 0;
      border-left: 4px solid #e74c3c;
      border-radius: 8px;
    }

    input[type="text"] {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 1rem;
      font-size: 1rem;
      background-color: #2a2a2a;
      color: #fff;
    }

    input[type="text"]::placeholder {
      color: #bbb;
    }

    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 0;
      }

      .content {
        margin: 2rem 1rem;
        width: auto;
      }

      .btn-action {
        width: 100%;
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>
<?php include ("navbar.php"); ?>
<div class="content-wrapper">
  <div class="content">
    <h1>Welcome, <?= $name ?>!</h1>
    <p>You are logged in as <strong><?= ucfirst($role) ?></strong>.</p>
    <p>This is your dashboard. Use the navigation or side panel to manage quizzes and take tests.</p>

    <?php if ($_SESSION['role'] === 'admin') : ?>
      <a href="create.php" class="btn-action">➕ Create New Quiz</a>
      <a href="ai-quiz.php" class="btn-action" style="background-color: #6f42c1;">🧠 Create New AI Quiz</a>
      <a href="manage.php" class="btn-action">🚩 Manage Your Quizzes</a>
    <?php endif; ?>

    <?php if ($_SESSION['role'] === 'user') : ?>
      <form action="join.php" method="POST" style="margin-top: 1rem;">
        <input type="text" name="quiz_code" placeholder="Enter Quiz Code" required>
        <button type="submit" class="btn-action">➕ Join Quiz</button>
      </form>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
