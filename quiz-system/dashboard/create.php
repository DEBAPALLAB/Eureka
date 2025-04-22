<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.html');
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
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Rubik', sans-serif;
      background: url('white.jpeg') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.75);
      z-index: 0;
    }

    .container {
      position: relative;
      z-index: 1;
      background: linear-gradient(135deg, rgba(40, 40, 40, 0.85), rgba(20, 20, 20, 0.85));
      border: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(255, 152, 0, 0.05);
      backdrop-filter: blur(12px);
      width: 100%;
      max-width: 600px;
      padding: 2.5rem 2rem;
      border-radius: 1.75rem;
      transition: all 0.3s ease-in-out;
    }

    .container:hover {
      box-shadow: 0 16px 50px rgba(0, 0, 0, 0.6), inset 0 0 25px rgba(255, 152, 0, 0.08);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #ff9800;
      font-size: 1.8rem;
      letter-spacing: 1px;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: 1px solid #555;
      border-radius: 10px;
      font-size: 1rem;
      background-color: rgba(255, 255, 255, 0.1);
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
  <div class="overlay"></div>
  <div class="container">
    <h2>Create a New Quiz</h2>
    <form action="create.php" method="POST">
      <input type="text" name="title" placeholder="Quiz Title" required>
      <textarea name="description" placeholder="Quiz Description" rows="4"></textarea>
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
