<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.html');
    exit;
}

$quiz_id = $_GET['quiz_id'];
$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM results WHERE user_id = $user_id AND quiz_id = $quiz_id");

if ($result->num_rows === 0) {
    echo "<p style='text-align:center;margin-top:2rem;'>Result not found for this quiz.</p>";
    exit;
}

$row = $result->fetch_assoc();
$score = $row['score'];
$total = $row['total'];
$percentage = ($score / $total) * 100;

$performance = '';
$color = '#ccc';

if ($percentage < 50) {
    $performance = "Needs Improvement";
    $color = "#f44336";
} elseif ($percentage < 75) {
    $performance = "Fair";
    $color = "#fbc02d";
} elseif ($percentage < 90) {
    $performance = "Good";
    $color = "#d4e157";
} else {
    $performance = "Excellent";
    $color = "#4caf50";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quiz Result</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 700px;
      margin: 4rem auto;
      background: #fff;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      text-align: center;
    }

    .result-box {
      background: <?= $color ?>;
      color: #fff;
      padding: 1.5rem;
      border-radius: 1rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    h2 {
      font-size: 26px;
      margin-bottom: 10px;
    }

    .chart-container {
      width: 100%;
      height: 300px;
    }

    .btn {
      display: inline-block;
      margin-top: 2rem;
      background: #4e73df;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: #2e59d9;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="result-box" style="background: <?= $color ?>;">
      <h2>Your Score: <?= $score ?> / <?= $total ?> (<?= round($percentage, 2) ?>%)</h2>
      <p style="font-size: 1.2rem;">Performance: <strong><?= $performance ?></strong></p>
    </div>

    <div class="chart-container">
      <canvas id="scoreChart"></canvas>
    </div>

    <a href="index.php" class="btn">⬅ Back to Dashboard</a>
  </div>

  <script>
    const ctx = document.getElementById('scoreChart').getContext('2d');
    const scoreChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Correct', 'Incorrect'],
        datasets: [{
          label: 'Score Breakdown',
          data: [<?= $score ?>, <?= $total - $score ?>],
          backgroundColor: ['#4caf50', '#e74c3c'],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#333',
              font: { size: 14 }
            }
          }
        }
      }
    });
  </script>
</body>
</html>