<?php
session_start();
require_once '../../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.html');
    exit;
}

$quiz_id = $_GET['quiz_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Question</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: #f5f7fa;
      padding: 40px;
    }

    .container {
      background: white;
      max-width: 700px;
      margin: auto;
      padding: 2rem;
      border-radius: 1.5rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #444;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: 1px solid #ddd;
      border-radius: 10px;
      font-size: 1rem;
    }

    button {
      background: #ff7043;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 10px;
      font-size: 1rem;
      cursor: pointer;
      width: 100%;
      transition: background 0.3s;
    }

    button:hover {
      background: #f4511e;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 1.2rem;
      text-decoration: none;
      color: #444;
      font-size: 0.95rem;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Add Question</h2>
    <form action="add-question.php?quiz_id=<?= $quiz_id ?>" method="POST">
      <textarea name="question" placeholder="Enter the question here..." required></textarea>
      <input type="text" name="option_a" placeholder="Option A" required>
      <input type="text" name="option_b" placeholder="Option B" required>
      <input type="text" name="option_c" placeholder="Option C" required>
      <input type="text" name="option_d" placeholder="Option D" required>
      <input type="text" name="correct_option" placeholder="Correct Option (A, B, C, or D)" required>
      <button type="submit">Add Question</button>
    </form>
    <a href="manage.php" class="back-link">← Back to Manage Quizzes</a>
  </div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'];
    $a = $_POST['option_a'];
    $b = $_POST['option_b'];
    $c = $_POST['option_c'];
    $d = $_POST['option_d'];
    $correct = $_POST['correct_option'];

    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_ans) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $quiz_id, $question, $a, $b, $c, $d, $correct);
    $stmt->execute();
    header("Location: add-question.php?quiz_id=" . $quiz_id); // Stay on same quiz
}
?>
