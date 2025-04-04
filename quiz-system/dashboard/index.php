<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.html");
    exit;
}

// Get session values
$name = htmlspecialchars($_SESSION['name']);
$role = htmlspecialchars($_SESSION['role']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Quiz System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .logout-btn {
            margin-top: 1rem;
            display: inline-block;
            padding: 8px 16px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome, <?= $name ?>!</h1>
    <p>You are logged in as <strong><?= ucfirst($role) ?></strong>.</p>

    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</div>
</body>
</html>
