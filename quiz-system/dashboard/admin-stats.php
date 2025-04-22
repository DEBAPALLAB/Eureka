<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.html');
    exit;
}

include("navbar.php");

// Fetch all users (non-admins)
$stmt = $conn->prepare("SELECT id, name FROM users WHERE role = 'user'");
$stmt->execute();
$users_result = $stmt->get_result();

$user_stats = [];

while ($user = $users_result->fetch_assoc()) {
    $uid = $user['id'];
    $name = htmlspecialchars($user['name']);

    $stats_stmt = $conn->prepare("SELECT COUNT(*) AS attempts, AVG(score) AS avg_score FROM user_results WHERE user_id = ?");
    $stats_stmt->bind_param("i", $uid);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result()->fetch_assoc();

    $user_stats[] = [
        'id' => $uid,
        'name' => $name,
        'attempts' => $stats_result['attempts'] ?? 0,
        'avg_score' => $stats_result['avg_score'] !== null ? round($stats_result['avg_score'], 2) : 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - All User Statistics</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
 <style>
  body {
  font-family: 'Poppins', sans-serif;
  
  background: url('white.jpeg') no-repeat center center fixed;
  background-size: cover;
  color: #f5f5f5;
}

.content-wrapper {
  margin-left: 220px;
  padding: 40px;
}

h2 {
  margin-bottom: 1rem;
  color: #ffa500;
  background-color: rgba(0, 0, 0, 0.75);
  padding: 1rem;
      border-radius: 12px;
      text-align: center;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #1e1e1e;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

th, td {
  padding: 12px;
  border-bottom: 1px solid #333;
  text-align: center;
  color: #f0f0f0;

}

th {
  background: #2c2c2c;
  color: #ffa500;
}

tr:hover {
  background: #2a2a2a;
}

.btn-view {
  background: #ffa500;
  color: black;
  padding: 6px 12px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.9rem;
  transition: background 0.3s ease;
}

.btn-view:hover {
  background: #e68a00;
}


    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 0;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
<div class="content-wrapper">
  <h2>All User Statistics</h2>
  <table>
    <thead>
      <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Total Attempts</th>
        <th>Average Score (%)</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($user_stats as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= $u['name'] ?></td>
          <td><?= $u['attempts'] ?></td>
          <td><?= $u['avg_score'] ?></td>
          <td><a class="btn-view" href="view-user-stats.php?user_id=<?= $u['id'] ?>">View Stats</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
