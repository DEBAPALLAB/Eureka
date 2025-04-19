<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.html');
    exit;
}

if (!isset($_SESSION['answers']) || !isset($_SESSION['quiz_id'])) {
    echo "Session expired. Please rejoin the quiz.";
    exit;
}

$quiz_id = $_SESSION['quiz_id'];
$user_answers = $_SESSION['answers'];

// Fetch quiz title
$title_stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$title_stmt->bind_param("i", $quiz_id);
$title_stmt->execute();
$title_result = $title_stmt->get_result();
$quiz_title = "Quiz";
if ($title_result->num_rows > 0) {
    $quiz_title = $title_result->fetch_assoc()['title'];
}

$sql = "SELECT id, correct_ans FROM questions WHERE quiz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

$total_questions = $result->num_rows;
$correct = 0;
$answered_qids = array_keys($user_answers);
$all_question_ids = [];

while ($row = $result->fetch_assoc()) {
  $qid = $row['id'];
  $correct_option = strtoupper(trim($row['correct_ans']));
  $all_question_ids[] = $qid;

  if (isset($user_answers[$qid]) && strtoupper(trim($user_answers[$qid])) === $correct_option) {
      $correct++;
  }
}

$unanswered = 0;
foreach ($all_question_ids as $qid) {
    if (!isset($user_answers[$qid]) || $user_answers[$qid] === null || $user_answers[$qid] === '') {
        $unanswered++;
    }
}

$incorrect = $total_questions - $correct - $unanswered;
$percentage = $total_questions > 0 ? round(($correct / $total_questions) * 100, 2) : 0;

// Determine performance message and color
if ($percentage >= 90) {
    $message = "Excellent work! You're a quiz master 🎉";
    $color = "#4CAF50"; // Green
} elseif ($percentage >= 75) {
    $message = "Great job! Almost perfect 💪";
    $color = "#9ACD32"; // Lime green
} elseif ($percentage >= 50) {
    $message = "Good effort! Keep practicing 👍";
    $color = "#fbc02d"; // Yellow
} elseif ($percentage >= 30) {
    $message = "Not bad, but there's room for improvement 🔄";
    $color = "#FF9800"; // Orange
} else {
    $message = "Keep trying, you'll get there! 💡";
    $color = "#e53935"; // Red
}

// Clear answers session so refresh doesn't resubmit
unset($_SESSION['answers']);
unset($_SESSION['quiz_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Result</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 40px;
        }

        .result-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 1rem;
        }

        .score-box {
            background-color: <?= $color ?>;
            color: white;
            font-size: 2rem;
            font-weight: 600;
            padding: 1.5rem;
            border-radius: 16px;
            margin: 1.5rem 0;
        }

        .stats {
            margin: 1.2rem 0;
            color: #555;
            font-size: 1.1rem;
        }

        .btn {
            display: inline-block;
            margin-top: 2rem;
            padding: 12px 24px;
            background: #4e73df;
            color: white;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #3b5fc2;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <h1><?= htmlspecialchars($quiz_title) ?></h1>
        <h2>Your Score</h2>
        <div class="score-box">
            <?= $percentage ?>%<br>
            <small style="font-size: 1rem; font-weight: 400;"><?= $message ?></small>
        </div>
        <div class="stats">
            <p><strong>Total Questions:</strong> <?= $total_questions ?></p>
            <p><strong>Correct Answers:</strong> <?= $correct ?></p>
            <p><strong>Incorrect Answers:</strong> <?= $incorrect ?></p>
            <p><strong>Unanswered:</strong> <?= $unanswered ?></p>
        </div>
        <a href="../index.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>