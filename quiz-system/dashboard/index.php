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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      background-color: #f4f6f9;
      font-family: 'Poppins', sans-serif;
    }

    .content-wrapper {
      margin-left: 220px;
      transition: margin-left 0.3s ease;
    }

    .navbar {
      background: #4e73df;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      font-weight: 500;
    }

    .content {
      max-width: 800px;
      margin: 3rem auto;
      padding: 2rem;
      background: white;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    h1 {
      margin-bottom: 1rem;
      color: #333;
    }

    p {
      color: #666;
    }

    .btn-logout {
      background: #e74c3c;
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      color: white;
      font-weight: 500;
    }

    .btn-logout:hover {
      background: #c0392b;
    }

    .btn-action {
      display: inline-block;
      margin: 20px 10px 0 0;
      padding: 10px 20px;
      background-color: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
    }

    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 70px;
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
  </div>
</div>

</body>
</html>
