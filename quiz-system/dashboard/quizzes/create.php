<?php
session_start();
require_once '../../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Quiz</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: #f0f2f5;
      padding: 40px;
    }

    .container {
      background: #fff;
      max-width: 600px;
      margin: 0 auto;
      padding: 2rem;
      border-radius: 1.5rem;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #333;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 1rem;
    }

    button {
      background: #673ab7;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 10px;
      cursor: pointer;
      font-size: 1rem;
      width: 100%;
      transition: all 0.3s ease;
    }

    button:hover {
      background: #5e35b1;
    }

    a.back {
      display: inline-block;
      margin-top: 1rem;
      text-decoration: none;
      color: #555;
    }

    a.back:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Create a New Quiz</h2>
    <form action="create.php" method="POST">
      <input type="text" name="title" placeholder="Quiz Title" required>
      <textarea name="description" placeholder="Quiz Description" rows="4" required></textarea>
      <input type="text" name="topic" placeholder="Quiz Topic (e.g., Math, History)" required>
      <button type="submit">Create Quiz</button>
    </form>
    <a href="manage.php" class="back">← Back to Manage Quizzes</a>
  </div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $topic = $_POST['topic'];

    $stmt = $conn->prepare("INSERT INTO quizzes (title, description, topic) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $topic);
    $stmt->execute();
    header("Location: manage.php");
}
?>
