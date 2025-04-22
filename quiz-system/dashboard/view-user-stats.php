<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.html');
    exit;
}

if (!isset($_GET['user_id'])) {
    echo "User ID not specified.";
    exit;
}

$view_user_id = intval($_GET['user_id']);

// Get user name
$user_stmt = $conn->prepare("SELECT name FROM users WHERE id = ? AND role = 'user'");
$user_stmt->bind_param("i", $view_user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    echo "User not found.";
    exit;
}

$user_name = htmlspecialchars($user_result->fetch_assoc()['name']);

// Get quiz results
$stmt = $conn->prepare("SELECT ur.*, q.title FROM user_results ur JOIN quizzes q ON ur.quiz_id = q.id WHERE ur.user_id = ?");
$stmt->bind_param("i", $view_user_id);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
$total_correct = 0;
$total_incorrect = 0;
$total_unanswered = 0;
$total_attempts = $result->num_rows;

while ($row = $result->fetch_assoc()) {
    $results[] = $row;
    $total_correct += $row['correct'];
    $total_incorrect += $row['incorrect'];
    $total_unanswered += $row['unanswered'];
}

$avg_correct = $total_attempts > 0 ? round($total_correct / $total_attempts, 2) : 0;
$avg_incorrect = $total_attempts > 0 ? round($total_incorrect / $total_attempts, 2) : 0;
$avg_unanswered = $total_attempts > 0 ? round($total_unanswered / $total_attempts, 2) : 0;

include("navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $user_name ?>'s Statistics</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      padding: 40px;
      transition: margin-left 0.3s ease;
    }

    .header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .header-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #fff;
  background-color: rgba(0, 0, 0, 0.75);
  padding: 1rem 2rem;
  border-radius: 12px;
  text-align: center;
  margin: 0 auto; /* Center horizontally */
  margin-left: 400px;
}

    .btn-back {
      background-color: orange;
      color: #fff;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: background 0.3s ease;
    }

    .btn-back:hover {
      background-color: #e69500;
    }

    .overview, .table-wrapper, .chart-container {
      background: rgba(0, 0, 0, 0.85);
      padding: 1.5rem;
      border-radius: 16px;
      box-shadow: 0 0 20px rgba(255, 165, 0, 0.4);
      margin-bottom: 2rem;
      text-align: left;
      color: #fff;
    }

    .stats-flex {
      display: flex;
      gap: 2rem;
      align-items: stretch;
      flex-wrap: wrap;
      max-width: 900px;
      margin: 0 auto 2rem auto;
    }

    .stats-flex .overview,
    .stats-flex .chart-container {
      flex: 1;
      min-width: 250px;
    }

    h2 {
      margin-bottom: 1rem;
      color: #fff;
      text-align: center;
    }

    p {
      margin-bottom: 0.5rem;
      color: #ddd;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      color: #fff;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #555;
    }

    th {
      background: rgba(255, 255, 255, 0.1);
      color: #f2f2f2;
    }

    tr:hover {
      background: rgba(255, 255, 255, 0.08);
      transition: background 0.3s ease;
    }

    .btn-view {
      background: orange;
      color: #fff;
      padding: 6px 12px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 600;
      transition: background 0.3s ease;
    }

    .btn-view:hover {
      background-color: #e69500;
    }

    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 0;
        padding: 20px;
      }

      .header-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .btn-back {
        width: 100%;
        text-align: center;
      }

      .stats-flex {
        flex-direction: column;
        margin-bottom: 1.5rem;
      }
    }
  </style>
</head>
<body>

<div class="content-wrapper">
  <div class="header-bar">
    <div class="header-title">Statistics for <?= $user_name ?></div>
    <a class="btn-back" href="admin-stats.php">&larr; Back to User List</a>
  </div>

  <div class="stats-flex">
    <div class="overview">
     <h2>Average Stats</h2>
<table class="stats-table">
  <tbody>
    <tr>
      <td><strong>Correct:</strong></td>
      <td><?= $avg_correct ?></td>
    </tr>
    <tr>
      <td><strong>Incorrect:</strong></td>
      <td><?= $avg_incorrect ?></td>
    </tr>
    <tr>
      <td><strong>Unanswered:</strong></td>
      <td><?= $avg_unanswered ?></td>
    </tr>
    <tr>
      <td><strong>Total Quizzes Attempted:</strong></td>
      <td><?= $total_attempts ?></td>
    </tr>
  </tbody>
</table>

    </div>
    <div class="chart-container">
      <canvas id="chart"></canvas>
    </div>
  </div>

  <div class="table-wrapper">
    <h2>Attempted Quizzes</h2>
    <?php if ($total_attempts > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Quiz Title</th>
          <th>Correct</th>
          <th>Incorrect</th>
          <th>Unanswered</th>
          <th>Score (%)</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $i => $row): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= $row['correct'] ?></td>
          <td><?= $row['incorrect'] ?></td>
          <td><?= $row['unanswered'] ?></td>
          <td><?= round($row['score'], 2) ?>%</td>
          <td><?= date('d M Y, H:i', strtotime($row['attempted_at'])) ?></td>
          <td><a class="btn-view" href="quizzes/result.php?quiz_id=<?= $row['quiz_id'] ?>&user_id=<?= $view_user_id ?>">View Result</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>This user hasn't attempted any quizzes yet.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  const ctx = document.getElementById('chart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Correct', 'Incorrect', 'Unanswered'],

      datasets: [{
        data: [<?= $avg_correct ?>, <?= $avg_incorrect ?>, <?= $avg_unanswered ?>],
        backgroundColor: ['#4CAF50', '#FF5722', '#FFC107']
      }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });
</script>
</body>
</html>
