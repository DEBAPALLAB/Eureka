<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New AI Quiz</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f9;
      padding: 2rem;
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }

    h2 {
      margin-bottom: 1rem;
      color: #4e73df;
    }

    label {
      display: block;
      margin-top: 1rem;
      font-weight: 600;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 0.5rem;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 2rem;
      padding: 12px 20px;
      background-color: #4e73df;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
    }

    button:hover {
      background-color: #375ac4;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>🧠 Create New AI Quiz</h2>
  <form action="ai-quiz-process.php" method="POST">
    <label for="title">Quiz Title</label>
    <input type="text" name="title" id="title" required>

    <label for="quiz_code">Quiz Code</label>
    <input type="text" name="quiz_code" id="quiz_code" required>

    <label for="topic">Topic / Subject</label>
    <input type="text" name="subject" id="subject" required>

    <label for="difficulty">Difficulty Level</label>
    <select name="difficulty" id="difficulty" required>
      <option value="">Select</option>
      <option value="Easy">Easy</option>
      <option value="Medium">Medium</option>
      <option value="Hard">Hard</option>
    </select>

    <label for="num_questions">Number of Questions</label>
    <input type="number" name="num_questions" id="num_questions" min="1" max="50" required>

    <label for="instructions">Extra Instructions (optional)</label>
    <textarea name="instructions" id="instructions" rows="3"></textarea>

    <button type="submit">Generate Quiz using Gemini AI</button>
  </form>
</div>

</body>
</html>
