<?php
session_start();
require_once('../../config/db.php');

if (!isset($_SESSION['quiz_code']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$quiz_code = $_SESSION['quiz_code'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, title FROM quizzes WHERE quiz_code = ?");
$stmt->bind_param("s", $quiz_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Quiz not found.";
    exit;
}

$quiz = $result->fetch_assoc();
$quiz_id = $quiz['id'];
$quiz_title = htmlspecialchars($quiz['title']);
$_SESSION['quiz_id'] = $quiz_id;

$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions_result = $stmt->get_result();
$questions = $questions_result->fetch_all(MYSQLI_ASSOC);

$total_questions = count($questions);
$current = isset($_GET['q']) ? intval($_GET['q']) : 0;
$current = max(0, min($current, $total_questions - 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['answer'] ?? null;
    $_SESSION['answers'][$questions[$current]['id']] = $selected;

    if (isset($_POST['next'])) {
        header("Location: test.php?q=" . ($current + 1));
        exit;
    } elseif (isset($_POST['back'])) {
        header("Location: test.php?q=" . ($current - 1));
        exit;
    } elseif (isset($_POST['submit'])) {
        $_SESSION['quiz_id'] = $quiz_id;
        header("Location: result.php");
        exit;
    }
}

$question = $questions[$current];
$stored_answer = $_SESSION['answers'][$question['id']] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quiz</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
body {
  font-family: 'Poppins', sans-serif;
  background: #121212;
  padding: 40px;
  color: #f5f5f5;
  background: url('white.jpeg') no-repeat center center fixed;
  background-size: cover;
}

.container {
  background: #1e1e1e;
  max-width: 700px;
  margin: auto;
  padding: 2rem;
  border-radius: 1.5rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

.container h1 {
  color: #4e73df; /* keep the blue */
  margin-bottom: 1rem;
  font-size: 28px;
}

h3 {
  color: #ffa726; /* orange subheading */
  margin-bottom: 1rem;
}

.option {
  margin-bottom: 1rem;
}

.option input {
  margin-right: 10px;
}

button {
  background: #4e73df; /* keep the blue */
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  font-size: 1rem;
  cursor: pointer;
  margin-right: 10px;
}

button:hover {
  background: #2e59d9;
}

a.back {
  display: block; /* block-level for full width */
  width: fit-content; /* only as wide as its content */
  margin: 2rem auto 0 auto; /* top margin, auto left/right */
  text-decoration: none;
  color: #ffa726; /* orange link */
  background-color: rgba(0, 0, 0, 0.75);
  padding: 1rem 2rem;
  border-radius: 12px;
  text-align: center;
}

a.back:hover {
  text-decoration: underline;
  color: #ff9800;
  
}

  </style>
</head>
<body>
<div class="container">
  <form method="POST">
    <h1><?= $quiz_title ?></h1>
    <h3>Question <?= $current + 1 ?> of <?= $total_questions ?></h3>
    <p><?= htmlspecialchars($question['question']) ?></p>

    <?php foreach (['a' => 'option_a', 'b' => 'option_b', 'c' => 'option_c', 'd' => 'option_d'] as $key => $opt): ?>
      <div class="option">
        <label>
          <input type="radio" name="answer" value="<?= strtoupper($key) ?>" <?= ($stored_answer === strtoupper($key)) ? 'checked' : '' ?>>
          <?= htmlspecialchars($question[$opt]) ?>
        </label>
      </div>
    <?php endforeach; ?>

    <?php if ($current > 0): ?>
      <button type="submit" name="back">Back</button>
    <?php endif; ?>

    <?php if ($current < $total_questions - 1): ?>
      <button type="submit" name="next">Next</button>
    <?php else: ?>
      <button type="submit" name="submit">Submit</button>
    <?php endif; ?>
  </form>
</div>
<a href="../index.php" class="back">← Back to Dashboard</a>

</body>
</html>
