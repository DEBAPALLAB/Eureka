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

// Update logic
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
        $stmt->execute();
        $question = $stmt->get_result()->fetch_assoc();
    }
}

// Add logic
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
  <title>Edit & Add Questions</title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      margin: 0;
      padding: 0;
      background: url('white.jpeg') no-repeat center center fixed;
      background-size: cover;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.75);
      min-height: 100vh;
      padding: 60px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .card {
      background: rgba(40, 40, 40, 0.85);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
      padding: 30px;
      width: 100%;
      max-width: 600px;
      margin-bottom: 40px;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    h2 {
      color: #ff9800;
      text-align: center;
      margin-bottom: 1rem;
    }

    form label {
      display: block;
      margin-top: 1rem;
      font-weight: 600;
      color: #f0f0f0;
    }

    input, textarea {
      width: 100%;
      padding: 8px;
      margin-top: 0.5rem;
      border-radius: 10px;
      border: 1px solid #444;
      background-color: rgba(255, 255, 255, 0.08);
      color: #fff;
      font-size: 1rem;
    }

    input:focus, textarea:focus {
      outline: none;
      border-color: #ff9800;
      box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.2);
    }

    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background-color: #ff9800;
      color: #000;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #e88d00;
    }

    .success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
    }

    .back-btn {
      text-align: center;
      background: #2c3e50;
      color: white;
      padding: 12px 20px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s;
    }

    .back-btn:hover {
      background: #1a252f;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <?php if ($success): ?>
      <div class="card">
        <div class="success"><?= htmlspecialchars($success) ?></div>
      </div>
    <?php endif; ?>

    <!-- Edit Existing Question -->
    <div class="card">
      <h2>✏️ Edit Existing Question</h2>
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
    </div>

    <!-- Add New Question -->
    <div class="card">
      <h2>➕ Add New Question</h2>
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
    </div>

    <a href="edit.php?id=<?= $quiz_id ?>" class="back-btn">← Back to Quiz</a>
  </div>
</body>
</html>