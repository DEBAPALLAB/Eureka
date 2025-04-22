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
  background: url('white.jpeg') no-repeat center center fixed;
  background-size: cover;
  
  color: #f5f5f5;
}

h2 {
  margin-bottom: 1rem;
  font-size: 2rem;
  color: #ffa500;
  background-color: rgba(0, 0, 0, 0.75);
  padding: 1rem;
      border-radius: 12px;
      text-align: center;
}

form {
  background: #1e1e1e;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
  max-width: 600px;
  margin-bottom: 40px;

  /* Ensure child elements respect the inner spacing */
  box-sizing: border-box;
}

label {
  display: block;
  margin: 1rem 0 0.5rem;
  color: #f0f0f0;
  padding-left: 2px; /* slight padding for alignment consistency */
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #555;
  border-radius: 8px;
  font-size: 1rem;
  background: #2c2c2c;
  color: #f5f5f5;

  /* Add consistent internal spacing */
  box-sizing: border-box;
}


.btn {
  margin-top: 1rem;
  background: #ffa500;
  color: black;
  border: none;
  padding: 10px 20px;
  font-size: 1rem;
  border-radius: 8px;
  cursor: pointer;
}

.btn:hover {
  background: #e68a00;
}

.message {
  background: #2e7d32;
  border: 1px solid #66bb6a;
  padding: 10px;
  margin-bottom: 20px;
  border-radius: 8px;
  color: #d4f8d4;
}

.questions-section {
  margin-top: 40px;
}

.questions-section h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #ffa500;
  background-color: rgba(0, 0, 0, 0.75);
  padding: 1rem;
      border-radius: 12px;
      text-align: center;
}

.question-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 20px;
}

.question-card {
  background: #1e1e1e;
  border: 2px solid #333;
  border-radius: 16px;
  padding: 16px;
  box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.2);
  position: relative;
  transition: 0.2s ease;
}

.question-card:hover {
  border-color: #ff9800;
  transform: translateY(-2px);
}

.question-text {
  font-size: 1rem;
  margin-bottom: 12px;
  color: #f0f0f0;
}

.question-actions {
  display: flex;
  justify-content: space-between;
}

.add-btn {
  display: inline-block;
  margin: 20px 0;
  background: #ffa500;
  color: black;
  padding: 10px 18px;
  border-radius: 10px;
  text-decoration: none;
  font-size: 1rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
  transition: background 0.3s ease;
}

.add-btn:hover {
  background: #e68a00;
}

.question-actions a {
  text-decoration: none;
  font-size: 0.9rem;
  background: #333;
  color: #ffa500;
  padding: 6px 10px;
  border-radius: 6px;
}

.question-actions a.delete {
  background: #e74c3c;
  color: white;
}

.main-content {
  margin-left: 220px;
  transition: margin-left 0.3s ease;
  padding: 40px;
}

.sidebar.minimized ~ .main-content {
  margin-left: 70px;
}

a.back {
  display: in;
  margin-top: 1rem;
  text-decoration: none;
  background: #2c2c2c;
  color: #f5f5f5;
  padding: 6px 10px;
  border-radius: 6px;

}

a.back:hover {
  text-decoration: underline;
}
</style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="main-content" id="mainContent">
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
