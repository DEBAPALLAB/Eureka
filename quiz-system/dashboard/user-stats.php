<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../views/login.html');
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
$user_name = 'User';

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
  background: #121212;
  color: #f5f5f5;
  background: url('white.jpeg') no-repeat center center fixed;
  background-size: cover;
}

.sticky-header {
  border-radius: 15px;
  text-align: center;
  top: 0;
  z-index: 1000;
  background: rgba(0,0,0,0.75);
  padding: 20px 40px;
  border-bottom: 1px solid #333;
  font-size: 1.8rem;
  font-weight: 600;
  color: #ffa726;
  margin-left: 580px;
  margin-right: 400px;
  margin-top: 30px; 
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
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
  background: rgba(0,0,0,0.75);
  padding: 1.5rem;
  border-radius: 20px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.stats-summary {
  line-height: 1.8;
  color: #f5f5f5;
  text-align: center;
}

.table-wrapper {
  background: rgba(0,0,0,0.75);
  border-radius: 20px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
  padding: 1.5rem;
  text-align: center;
}

table {
  width: 100%;
  border-collapse: collapse;
  color: #f5f5f5;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #333;
}

th {
  background: #2c2c2c;
  color: #ffa726;
}

tr:hover {
  background: #2c2c2c;
}

.btn-view {
  background: #ffa726;
  color: #1e1e1e;
  padding: 6px 12px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 0.9rem;
  transition: background 0.3s ease;
}

.btn-view:hover {
  background: #fb8c00;
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

<?php include("navbar.php"); ?>

<div class="sticky-header"><?= $user_name ?>'s Statistics</div>

<div class="content-wrapper">
<div class="box-container">
  <div class="overview">
    <div class="stats-summary" style="flex: 1;">
      <h2>Your Average Stats</h2>
      <table class="stats-table">
        <tr>
          <th>Stat</th>
          <th>Value</th>
        </tr>
        <tr>
          <td>Correct Answers</td>
          <td><?= $avg_correct ?></td>
        </tr>
        <tr>
          <td>Incorrect Answers</td>
          <td><?= $avg_incorrect ?></td>
        </tr>
        <tr>
          <td>Unanswered</td>
          <td><?= $avg_unanswered ?></td>
        </tr>
        <tr>
          <td>Total Quizzes Attempted</td>
          <td><?= $total_attempts ?></td>
        </tr>
      </table>
    </div>
    <div class="chart-container" style="flex: 1; display: flex; justify-content: center; align-items: center;">
      <div style="width: 100%; max-width: 300px;">
  <canvas id="chart"></canvas>
</div>

    </div>
  </div>
</div>

<div class="box-container table-wrapper">
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
      position: 'bottom',
      align: 'center', 
      labels: {
        boxWidth: 20,
        padding: 15,
        font: {
          size: 14
        }
      }
    }
  }
}

  });
</script>

</body>
</html>
