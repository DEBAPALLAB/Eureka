<?php
$host = 'localhost';
$dbname = 'quiz_system';
$username = 'root';
$password = ''; // Leave blank for XAMPP/Laragon/MAMP defaults

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Enable exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
