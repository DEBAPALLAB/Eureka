<?php
require_once '../config/db.php';
session_start();

if (($_SESSION['role']) !== 'user' && $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.html");
    exit;
}

// Fetch average scores for each user
$query = "
    SELECT u.name, u.id AS user_id, 
           ROUND(AVG(ur.score), 2) AS avg_score
    FROM users u
    JOIN user_results ur ON u.id = ur.user_id
    WHERE u.role = 'user'
    GROUP BY u.id
    ORDER BY avg_score DESC
";

$result = $conn->query($query);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leaderboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
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
      text-align: center;
      border-bottom: 1px solid #eee;
      color : #f2f2f2;
    }
    th {
      background-color: orange;
      color: #000;
    }
    tr:hover {
      background-color:rgb(189, 157, 105);
    }
    .medal {
      font-size: 1.5rem;
    }
    .gold { color: gold; }
    .silver { color: silver; }
    .bronze { color: #cd7f32; }
    @media (max-width: 768px) {
      .content-wrapper { margin-left: 70px; }
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <div class="content-wrapper">
    <h2><u>Leaderboard</u></h2>
    <table>
      <thead>
        <tr>
          <th>Rank</th>
          <th>Name</th>
          <th>Average Score (%)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $index => $user): ?>
          <tr>
            <td>
              <?php
                if ($index === 0) echo '<span class="medal gold">🥇</span>'; // Gold Medal
                elseif ($index === 1) echo '<span class="medal silver">🥈</span>'; // Silver Medal
                elseif ($index === 2) echo '<span class="medal bronze">🥉</span>'; // Bronze Medal
                else echo $index + 1;
              ?>
            </td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= $user['avg_score'] ?>%</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>