<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.html");
    exit;
}

$question_id = $_GET['id'] ?? null;

if ($question_id) {
    $quiz_id_stmt = $conn->prepare("SELECT quiz_id FROM questions WHERE id = ?");
    $quiz_id_stmt->bind_param("i", $question_id);
    $quiz_id_stmt->execute();
    $result = $quiz_id_stmt->get_result();
    $quiz_id = $result->fetch_assoc()['quiz_id'] ?? 0;

    $delete = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $delete->bind_param("i", $question_id);
    $delete->execute();

    header("Location: edit.php?id=$quiz_id&deleted=1");
    exit;
}

echo "Invalid question ID.";
