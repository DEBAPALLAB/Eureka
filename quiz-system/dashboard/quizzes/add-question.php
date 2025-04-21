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

  .back-link {
    display: block;
    text-align: center;
    margin-top: 1rem;
    text-decoration: none;
    color: #ff9800;
    font-size: 0.95rem;
  }

  .back-link:hover {
    text-decoration: underline;
  }
</style>

</head>
<body>
  <div class="overlay"></div>
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