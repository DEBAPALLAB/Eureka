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
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
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
  color: #f5f5f5;
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
  max-width: 600px;
  width: 100%;
  background: linear-gradient(135deg, rgba(40, 40, 40, 0.85), rgba(20, 20, 20, 0.85));
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(255, 152, 0, 0.05);
  backdrop-filter: blur(16px);
  border-radius: 1.75rem;
  padding: 2.5rem 2rem;
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
  animation: fadeIn 0.8s ease-in-out;
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

label {
  display: block;
  margin-top: 1rem;
  font-weight: 600;
  color: #ccc;
}

input,
textarea,
select {
  width: 100%;
  padding: 12px;
  margin-top: 0.5rem;
  border-radius: 10px;
  border: 1px solid #555;
  background-color: rgba(255, 255, 255, 0.08);
  color: #f0f0f0;
  font-size: 1rem;
}

input::placeholder,
textarea::placeholder {
  color: #aaa;
}

input:focus,
textarea:focus,
select:focus {
  outline: none;
  border-color: #ff9800;
  box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.2);
}

select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23ff9800" d="M2 0L0 2h4L2 0zm0 5L0 3h4L2 5z"/></svg>');
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 12px;
  cursor: pointer;
}

button {
  margin-top: 2rem;
  padding: 12px 20px;
  background-color: #ff9800;
  color: #000;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  width: 100%;
  transition: all 0.3s ease;
}

button:hover {
  background-color: #e68900;
}
option {
  background-color: #222;
  color: #f5f5f5;
  padding: 10px;
  font-size: 1rem;
  border-bottom: 1px solid #444;
}
  </style>
</head>
<body>
  <div class="overlay"></div>

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
