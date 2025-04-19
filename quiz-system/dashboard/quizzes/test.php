<?php
session_start();
require_once('../../config/db.php');

if (!isset($_SESSION['quiz_code'])) {
    header("Location: index.php");
    exit;
}

$quiz_code = $_SESSION['quiz_code'];

$stmt = $conn->prepare("SELECT id FROM quizzes WHERE quiz_code = ?");
$stmt->bind_param("s", $quiz_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Quiz not found.";
    exit;
}

$quiz = $result->fetch_assoc();
$quiz_id = $quiz['id'];

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
    $_SESSION['answers'][$current] = $selected;

    if (isset($_POST['next'])) {
        header("Location: test.php?q=" . ($current + 1));
        exit;
    } elseif (isset($_POST['back'])) {
        header("Location: test.php?q=" . ($current - 1));
        exit;
    } elseif (isset($_POST['submit'])) {
        header("Location: result.php?quiz_id=$quiz_id");
        exit;
    }
}

$question = $questions[$current];
$stored_answer = $_SESSION['answers'][$current] ?? '';
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
      background: #f4f6f9;
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
      color: #444;
      margin-bottom: 1rem;
    }

    .option {
      margin-bottom: 1rem;
    }

    .option input {
      margin-right: 10px;
    }

    button {
      background: #4e73df;
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
  </style>
</head>
<body>
<div class="container">
  <form method="POST">
    <h2>Question <?= $current + 1 ?> of <?= $total_questions ?></h2>
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
</body>
</html>