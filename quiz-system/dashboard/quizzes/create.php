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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: rgba(0, 0, 0, 0.75);
      color: #f5f5f5;
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: rgba(0, 0, 0, 0.75);
      width: 100%;
      max-width: 600px;
      padding: 2rem;
      border-radius: 1.5rem;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.6);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #ff9800;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: 1px solid #555;
      border-radius: 10px;
      font-size: 1rem;
      background-color: rgba(255, 255, 255, 0.05);
      color: #f0f0f0;
    }

    input::placeholder, textarea::placeholder {
      color: #aaa;
    }

    input:focus, textarea:focus {
      outline: none;
      border-color: #ff9800;
      box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.2);
    }

    button {
      background: #ff9800;
      color: #000;
      border: none;
      padding: 12px 20px;
      border-radius: 10px;
      cursor: pointer;
      font-size: 1rem;
      width: 100%;
      transition: all 0.3s ease;
      font-weight: 600;
    }

    button:hover {
      background: #e68900;
    }

    a.back {
      display: inline-block;
      margin-top: 1rem;
      text-decoration: none;
      color: #ff9800;
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
      <input type="text" name="code" placeholder="Quiz Code" required>
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
    $code = $_POST['code'];

    $stmt = $conn->prepare("INSERT INTO quizzes (title, description, topic, quiz_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $topic, $code);
    $stmt->execute();
    header("Location: manage.php");
}
?>
