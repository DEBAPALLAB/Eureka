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
    .sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 220px;
  height: 100%;
  background-color: #4e73df;
  color: white;
  padding: 30px 20px;
  font-family: 'Rubik', sans-serif;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar h2 {
  font-size: 1.8rem;
  margin-bottom: 2rem;
  font-weight: 700;
}

.sidebar a {
  display: block;
  color: white;
  text-decoration: none;
  margin-bottom: 1rem;
  font-size: 1.1rem;
  transition: 0.3s ease;
}

.sidebar a:hover {
  padding-left: 8px;
  color: #ffefc3;
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
  </style>
</head>
<body>
<div class="sidebar">
  <h2>Quizzy</h2>
  <a>🏠 Home</a>
  <a href="../analytics/stats.php">📊 Analytics</a>
  <a href="quizzes/manage.php">📝 Manage Quizzes</a>
</div>

  <div class="navbar">
    <div><strong>Quiz Dashboard</strong></div>
    <div><a href="../auth/logout.php" class="btn-logout">Logout</a></div>
  </div>

  <div class="content">
    <h1>Welcome, <?= $name ?>!</h1>
    <p>You are logged in as <strong><?= ucfirst($role) ?></strong>.</p>
    <p>This is your dashboard. Use the navigation or side panel to manage quizzes and take tests.</p>
    <?php if ($_SESSION['role'] === 'admin') : ?>
    <a href="/quiz-system/dashboard/quizzes/create.php" 
       style="display: inline-block; margin: 20px 0; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">
        ➕ Create New Quiz
    </a>
    <a href="quizzes/manage.php"
    style="display: inline-block; margin: 20px 0; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">
        🚩 Manage Your Quizzes
    </a>

<?php endif; ?>

  </div>

</body>
</html>
