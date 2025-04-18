<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.html');
    exit;
}

$question_id = $_GET['id'] ?? null;
if (!$question_id) {
    echo "Question ID missing.";
    exit;
}

// Fetch question data
$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

if (!$question) {
    echo "Question not found.";
    exit;
}

$quiz_id = $question['quiz_id'];
$success = '';

// Update question logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $question_text = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_ans = $_POST['correct_ans'];

    $update = $conn->prepare("UPDATE questions SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_ans = ? WHERE id = ?");
    $update->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_ans, $question_id);

    if ($update->execute()) {
        $success = "Question updated successfully.";
        // Refresh question data
        $stmt->execute();
        $question = $stmt->get_result()->fetch_assoc();
    }
}

// Add new question logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new'])) {
    $new_question = $_POST['new_question'];
    $new_a = $_POST['new_a'];
    $new_b = $_POST['new_b'];
    $new_c = $_POST['new_c'];
    $new_d = $_POST['new_d'];
    $new_correct = $_POST['new_correct'];

    $insert = $conn->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_ans) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("issssss", $quiz_id, $new_question, $new_a, $new_b, $new_c, $new_d, $new_correct);

    if ($insert->execute()) {
        $success = "New question added!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Question</title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: #f5f5f5;
      padding: 40px;
      position: relative;
    }

    h2 {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    form {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      margin-bottom: 40px;
      max-width: 700px;
    }

    label {
      margin-top: 1rem;
      display: block;
      font-weight: 500;
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }

    button {
      margin-top: 20px;
      background: #4e73df;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 1rem;
      cursor: pointer;
    }

    button:hover {
      background: #365dc9;
    }

    .success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .section-title {
      margin-top: 60px;
      margin-bottom: 10px;
      font-size: 1.5rem;
    }

    .back-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #2c3e50;
      color: white;
      padding: 12px 20px;
      border-radius: 12px;
      font-size: 1rem;
      text-decoration: none;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      transition: 0.3s ease;
    }

    .back-btn:hover {
      background: #1a252f;
    }
  </style>
</head>
<body>

  <h2>Edit Question</h2>

  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="update" value="1">
    <label>Question</label>
    <textarea name="question" required><?= htmlspecialchars($question['question']) ?></textarea>

    <label>Option A</label>
    <input type="text" name="option_a" value="<?= htmlspecialchars($question['option_a']) ?>" required>

    <label>Option B</label>
    <input type="text" name="option_b" value="<?= htmlspecialchars($question['option_b']) ?>" required>

    <label>Option C</label>
    <input type="text" name="option_c" value="<?= htmlspecialchars($question['option_c']) ?>" required>

    <label>Option D</label>
    <input type="text" name="option_d" value="<?= htmlspecialchars($question['option_d']) ?>" required>

    <label>Correct Option (a/b/c/d)</label>
    <input type="text" name="correct_ans" value="<?= htmlspecialchars($question['correct_ans']) ?>" required>

    <button type="submit">Update Question</button>
  </form>

  <div class="section-title">Add New Question</div>

  <form method="POST">
    <input type="hidden" name="add_new" value="1">
    <label>Question</label>
    <textarea name="new_question" required></textarea>

    <label>Option A</label>
    <input type="text" name="new_a" required>

    <label>Option B</label>
    <input type="text" name="new_b" required>

    <label>Option C</label>
    <input type="text" name="new_c" required>

    <label>Option D</label>
    <input type="text" name="new_d" required>

    <label>Correct Option (a/b/c/d)</label>
    <input type="text" name="new_correct" required>

    <button type="submit">Add Question</button>
  </form>

  <!-- Floating Back Button -->
  <a href="edit.php?id=<?= $quiz_id ?>" class="back-btn">← Back to Quiz</a>

</body>
</html>
