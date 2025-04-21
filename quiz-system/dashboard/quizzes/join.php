<?php
session_start();
require_once('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_code = trim($_POST['quiz_code']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id FROM quizzes WHERE quiz_code = ?");
    $stmt->bind_param("s", $quiz_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $quiz = $result->fetch_assoc();
        $quiz_id = $quiz['id'];

        $checkStmt = $conn->prepare("SELECT id FROM user_results WHERE user_id = ? AND quiz_id = ?");
        $checkStmt->bind_param("ii", $user_id, $quiz_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult && $checkResult->num_rows > 0) {
            header("Location: result.php?quiz_id=$quiz_id&user_id=$user_id");
            exit;
        }

        $_SESSION['quiz_code'] = $quiz_code;
        unset($_SESSION['answers']);
        header("Location: ../quizzes/test.php?quiz_id=$quiz_id");
        exit;
    } else {
        $_SESSION['join_error'] = "Invalid quiz code. Please try again.";
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
} 
?>
