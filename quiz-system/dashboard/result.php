<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/login.html');
    exit;
}

$session_user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] === 'admin';

// Determine whose result to show
$quiz_id = $_SESSION['quiz_id'] ?? $_GET['quiz_id'] ?? null;
$user_id = $is_admin ? ($_GET['user_id'] ?? null) : $session_user_id;

if (!$quiz_id || !$user_id) {
    echo "Session expired or invalid request. Please rejoin the quiz.";
    exit;
}

// Fetch quiz title
$title_stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$title_stmt->bind_param("i", $quiz_id);
$title_stmt->execute();
$title_result = $title_stmt->get_result();
$quiz_title = ($title_result->num_rows > 0) ? $title_result->fetch_assoc()['title'] : "Quiz";

// Check if result already exists
$check_stmt = $conn->prepare("SELECT * FROM user_results WHERE user_id = ? AND quiz_id = ?");
$check_stmt->bind_param("ii", $user_id, $quiz_id);
$check_stmt->execute();
$result_check = $check_stmt->get_result();

if ($result_check->num_rows > 0) {
    $existing_result = $result_check->fetch_assoc();
    $correct = $existing_result['correct'];
    $incorrect = $existing_result['incorrect'];
    $unanswered = $existing_result['unanswered'];
    $total_questions = $existing_result['total_questions'];
    $percentage = $total_questions > 0 ? round(($correct / $total_questions) * 100, 2) : 0;
} else {
    if (!isset($_SESSION['answers'])) {
        echo "Session expired. Please rejoin the quiz.";
        exit;
    }

    $user_answers = $_SESSION['answers'];

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

    // Save result to DB
    $insert = $conn->prepare("INSERT INTO user_results (user_id, quiz_id, score, total_questions, correct, incorrect, unanswered) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iiiiiii", $user_id, $quiz_id, $percentage, $total_questions, $correct, $incorrect, $unanswered);
    $insert->execute();

    // Clear session data only if it's the logged-in user
    if (!$is_admin || $user_id == $session_user_id) {
        unset($_SESSION['answers']);
        unset($_SESSION['quiz_id']);
    }
}

// Determine performance message and color
if ($percentage >= 90) {
    $message = "Excellent work! You're a quiz master 🎉";
    $color = "#4CAF50";
} elseif ($percentage >= 75) {
    $message = "Great job! Almost perfect 💪";
    $color = "#9ACD32";
} elseif ($percentage >= 50) {
    $message = "Good effort! Keep practicing 👍";
    $color = "#fbc02d";
} elseif ($percentage >= 30) {
    $message = "Not bad, but there's room for improvement 🔄";
    $color = "#FF9800";
} else {
    $message = "Keep trying, you'll get there! 💡";
    $color = "#e53935";
}
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
    background: #121212;
    margin: 0;
    padding: 40px;
    color: #f5f5f5;
    background: url('white.jpeg') no-repeat center center fixed;
      background-size: cover;
}

.result-container {
    max-width: 600px;
    margin: auto;
    background: rgba(0, 0, 0, 0.75);
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
    text-align: center;
}

h1 {
    color: #ffa500;
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
    color: #e0e0e0;
    font-size: 1.1rem;
}

.btn {
    display: inline-block;
    margin-top: 2rem;
    padding: 12px 24px;
    background: #ffa500;
    color: white;
    border: none;
    border-radius: 12px;
    text-decoration: none;
    font-size: 1rem;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #ef6c00;
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
    <?php if ($is_admin && $user_id !== $session_user_id): ?>
        <a href="view-user-stats.php?user_id=<?= $user_id ?>" class="btn">Back to User Stats</a>
    <?php else: ?>
        <a href="index.php" class="btn">Back to Dashboard</a>
    <?php endif; ?>
</div>
</body>
</html>
