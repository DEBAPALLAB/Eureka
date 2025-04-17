<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.html");
    exit;
}

$name = htmlspecialchars($_SESSION['name']);
$role = htmlspecialchars($_SESSION['role']);
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
      background-color: #f4f6f9;
      font-family: 'Poppins', sans-serif;
      color: #333;
    }

    .content-wrapper {
      margin-left: 220px;
      transition: margin-left 0.3s ease;
    }

    .navbar {
      background: #4e73df;
      color: #fff;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
    }

    .btn-logout {
      background: #e74c3c;
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      color: #fff;
      font-weight: 500;
      transition: background 0.3s ease;
    }

    .btn-logout:hover {
      background: #c0392b;
    }

    .content {
      max-width: 800px;
      margin: 3rem auto;
      padding: 2rem;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .content h1 {
      font-size: 28px;
      margin-bottom: 1rem;
    }

    .content p {
      margin-bottom: 1rem;
      color: #555;
    }

    .btn-action {
      display: inline-block;
      margin: 20px 10px 0 0;
      padding: 10px 20px;
      background-color: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.3s ease;
    }

    .btn-action:hover {
      background-color: #0056b3;
    }

    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 0;
      }

      .content {
        margin: 2rem 1rem;
      }

      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .btn-action {
        width: 100%;
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>

<?php include("navbar.php"); ?>

<div class="content-wrapper">
  <div class="navbar">
    <div><strong>Quiz Dashboard</strong></div>
    <div><a href="../auth/logout.php" class="btn-logout">Logout</a></div>
  </div>

  <div class="content">
    <h1>Welcome, <?= $name ?>!</h1>
    <p>You are logged in as <strong><?= ucfirst($role) ?></strong>.</p>
    <p>This is your dashboard. Use the navigation or side panel to manage quizzes and take tests.</p>

    <?php if ($_SESSION['role'] === 'admin') : ?>
      <a href="/quiz-system/dashboard/quizzes/create.php" class="btn-action">➕ Create New Quiz</a>
      <a href="/quiz-system/dashboard/quizzes/manage.php" class="btn-action">🚩 Manage Your Quizzes</a>
    <?php endif; ?>

    <?php if ($_SESSION['role'] === 'user') : ?>
      <a href="/quiz-system/dashboard/quizzes/create.php" class="btn-action">➕ Join Quiz</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
