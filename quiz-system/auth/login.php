<?php
session_start();
require_once '../config/db.php';
require_once 'validate.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $email    = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // don't sanitize password text itself

    // Validate email format
    if (!isValidEmail($email)) {
        die("Invalid email format.");
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];

        header("Location: ../dashboard/index.php");
        exit;
    } else {
        die("Incorrect email or password.");
    }
}
?>
