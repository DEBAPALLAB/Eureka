<?php
require_once '../../config/db.php';
session_start();

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.html');
    exit;
}

$quiz_id = $_GET['id'] ?? null;
if (!$quiz_id) {
    echo "Quiz ID missing.";
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['title'] ?? '';
    $new_topic = $_POST['topic'] ?? '';

    $stmt = $conn->prepare("UPDATE quizzes SET title = ?, topic = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_name, $new_topic, $quiz_id);
    if ($stmt->execute()) {
        $message = "Quiz updated successfully!";
    } else {
        $message = "Error updating quiz.";
    }
}

// Fetch quiz
$quizStmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
$quizStmt->bind_param("i", $quiz_id);
$quizStmt->execute();
$quizResult = $quizStmt->get_result();
$quiz = $quizResult->fetch_assoc();
if (!$quiz) {
    echo "Quiz not found.";
    exit;
}

// Fetch questions
$qStmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$qStmt->bind_param("i", $quiz_id);
$qStmt->execute();
$questions = $qStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background-color: #121212;
      color: #e0e0e0;
      margin: 0;
      display: flex;
    }

    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 220px;
      height: 100vh;
      background-color: #1f1f1f;
      padding: 20px;
      display: flex;
      flex-direction: column;
      border-top-right-radius: 20px;
      border-bottom-right-radius: 20px;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.7);
      z-index: 1000;
    }

    .sidebar .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: #ff8c00;
      margin-bottom: 1.5rem;
    }

    .sidebar-btn {
      display: block;
      margin: 8px 0;
      padding: 10px 12px;
      background-color: #ff8c00;
      color: #1f1f1f;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar-btn:hover {
      background-color: #e07b00;
    }

    .logout-btn {
      margin-top: auto;
      background-color: #e74c3c;
      color: #fff;
    }
    .logout-btn:hover {
      background-color: #c0392b;
    }

    /* Main Content */
    .main-content {
      margin-left: 240px;
      padding: 40px;
      flex: 1;
      overflow-y: auto;
    }

    h2 {
      color: #ff8c00;
      margin-bottom: 1rem;
      font-size: 2rem;
    }

    form {
      background-color: #1f1f1f;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.5);
      max-width: 600px;
      margin-bottom: 40px;
    }

    label {
      display: block;
      margin: 1rem 0 0.5rem;
      color: #ff8c00;
    }

    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #333;
      border-radius: 8px;
      background-color: #121212;
      color: #e0e0e0;
      font-size: 1rem;
    }

    .btn {
      margin-top: 1rem;
      background-color: #ff8c00;
      color: #1f1f1f;
      border: none;
      padding: 10px 20px;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn:hover {
      background-color: #e07b00;
    }

    .message {
      background-color: #2e7d32;
      color: #e8f5e9;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 8px;
    }

    .questions-section {
      margin-top: 40px;
    }

    .questions-section h3 {
      color: #ff8c00;
      margin-bottom: 1rem;
      font-size: 1.5rem;
    }

    .question-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }

    .question-card {
      background-color: #1f1f1f;
      border: 1px solid #333;
      border-radius: 16px;
      padding: 16px;
      box-shadow: 2px 2px 12px rgba(0,0,0,0.5);
      transition: transform 0.2s, border-color 0.2s;
    }

    .question-card:hover {
      transform: translateY(-2px);
      border-color: #ff8c00;
    }

    .question-text {
      font-size: 1rem;
      margin-bottom: 12px;
    }

    .question-actions {
      display: flex;
      justify-content: space-between;
    }

    .question-actions a {
      padding: 6px 10px;
      border-radius: 6px;
      font-size: 0.9rem;
      text-decoration: none;
      background-color: #ff8c00;
      color: #1f1f1f;
      transition: background 0.3s;
    }

    .question-actions a:hover {
      background-color: #e07b00;
    }

    .question-actions a.delete {
      background-color: #e74c3c;
      color: #fff;
    }

    .question-actions a.delete:hover {
      background-color: #c0392b;
    }

    .add-btn {
      display: inline-block;
      margin: 20px 0;
      background-color: #1abc9c;
      color: white;
      padding: 10px 18px;
      border-radius: 10px;
      text-decoration: none;
      font-size: 1rem;
      transition: background 0.3s;
    }

    .add-btn:hover {
      background-color: #16a085;
    }

    .main-content a.back {
      color: #e0e0e0;
      display: inline-block;
      margin-top: 1rem;
      text-decoration: none;
    }

    .main-content a.back:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="sidebar">
  <h2 class="logo">Quizzy</h2>
  <a class="sidebar-btn" href="index.php">🏠 Home</a>
  <a class="sidebar-btn" href="stats.php">📊 Stats</a>
  <a class="sidebar-btn" href="register.php">📝 Register</a>
  <a class="sidebar-btn logout-btn" href="../../auth/logout.php">🚪 Logout</a>
</div>

<div class="main-content">
  <h2>Edit Quiz</h2>

  <?php if ($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="title">Quiz Name</label>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($quiz['title'] ?? '') ?>" required>

    <label for="topic">Topic</label>
    <input type="text" name="topic" id="topic" value="<?= htmlspecialchars($quiz['topic']) ?>" required>

    <button type="submit" class="btn">Save Changes</button>
  </form>

  <a href="add-question.php?quiz_id=<?= $quiz_id ?>" class="add-btn">➕ Add New Question</a><br>
  <a href="manage.php" class="back">← Back to Manage Quizzes</a>

  <div class="questions-section">
    <h3>QUESTIONS</h3>
    <div class="question-grid">
      <?php while ($q = $questions->fetch_assoc()): ?>
        <div class="question-card">
          <div class="question-text"><?= htmlspecialchars(mb_strimwidth($q['question'], 0, 70, '...')) ?></div>

          <div class="question-actions">
            <a href="edit-question.php?id=<?= $q['id'] ?>">Edit</a>
            <a href="delete-question.php?id=<?= $q['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
</body>
</html>
