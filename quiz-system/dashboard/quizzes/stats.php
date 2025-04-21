<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all attempts
$stmt = $conn->prepare("SELECT ur.*, q.title FROM user_results ur JOIN quizzes q ON ur.quiz_id = q.id WHERE ur.user_id = ?");
$stmt->bind_param("i", $user_id);
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

// Fetch user's name from the database
$user_name = 'User'; // Default fallback

$user_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_row = $user_result->fetch_assoc()) {
    $user_name = htmlspecialchars($user_row['name']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Statistics</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #f5f7fa;
    }

    .sticky-header {
      border-radius : 15px;
      text-align : center;
      position: sticky;
      top: 0;
      z-index: 1000;
      background: #ffffff;
      padding: 20px 40px;
      border-bottom: 1px solid #ddd;
      font-size: 1.8rem;
      font-weight: 600;
      color: #333;
      margin-left: 220px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .content-wrapper {
      margin-left: 220px;
      padding: 40px;
      transition: margin-left 0.3s ease;
    }

    .overview {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      background: #fff;
      padding: 1.5rem;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    .stats-summary {
      line-height: 1.8;
    }

    .table-wrapper {
      background: white;
      border-radius: 20px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      padding: 1.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #e0e0e0;
    }

    th {
      background: #f0f2f7;
      color: #444;
    }

    tr:hover {
      background: #f9f9f9;
    }

    .btn-view {
      background: #4e73df;
      color: white;
      padding: 6px 12px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .btn-view:hover {
      background: #3b5fc2;
    }

    @media (max-width: 768px) {
      .sticky-header,
      .content-wrapper {
        margin-left: 0;
        padding: 20px;
      }

      .overview {
        flex-direction: column;
        gap: 20px;
      }
    }
  </style>
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="sticky-header"><?= $user_name ?>'s Statistics</div>

<div class="content-wrapper">
  <div class="overview">
    <div class="stats-summary" style="flex: 1;">
      <h2>Your Average Stats</h2>
      <p><strong>Correct Answers:</strong> <?= $avg_correct ?></p>
      <p><strong>Incorrect Answers:</strong> <?= $avg_incorrect ?></p>
      <p><strong>Unanswered:</strong> <?= $avg_unanswered ?></p>
      <p><strong>Total Quizzes Attempted:</strong> <?= $total_attempts ?></p>
    </div>
    <div class="chart-container" style="flex: 1; display: flex; justify-content: center; align-items: center;">
      <div style="width: 100%; max-width: 250px; aspect-ratio: 1;">
        <canvas id="chart" style="width: 100%; height: 100%;"></canvas>
      </div>
    </div>
  </div>

  <div class="table-wrapper">
    <h2>Attempted Quizzes</h2>
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
            <td><a class="btn-view" href="result.php?quiz_id=<?= $row['quiz_id'] ?>">View Result</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  const ctx = document.getElementById('chart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Correct', 'Incorrect', 'Unanswered'],
      datasets: [{
        label: 'Average Quiz Breakdown',
        data: [<?= $avg_correct ?>, <?= $avg_incorrect ?>, <?= $avg_unanswered ?>],
        backgroundColor: ['#4CAF50', '#FF5722', '#FFC107'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
</script>

</body>
</html>
