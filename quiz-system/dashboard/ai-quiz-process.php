<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Unauthorized access');
}

$quizCode = $_POST['quiz_code'] ?? '';
$numQuestions = $_POST['num_questions'] ?? 0;
$subject = $_POST['subject'] ?? '';
$difficulty = $_POST['difficulty'] ?? '';

if (!$quizCode || !$numQuestions || !$subject || !$difficulty) {
    die('Missing required fields.');
}

$schema = <<<EOD
Schema for the quizzes table:
- id: INT, AUTO_INCREMENT, PRIMARY KEY
- title: VARCHAR(255)
- topic: VARCHAR(255)
- quiz_code: VARCHAR(100)
- created_at: TIMESTAMP

Schema for the questions table:
- id: INT, AUTO_INCREMENT, PRIMARY KEY
- quiz_id: INT (foreign key to quizzes.id)
- question: TEXT
- option_a: TEXT
- option_b: TEXT
- option_c: TEXT
- option_d: TEXT
- correct_ans: ENUM('A','B','C','D')
EOD;

$prompt = "Generate a MySQL query to insert a quiz with the following details:\n\n" .
    "Title: 'AI Quiz on $subject'\n" .
    "Topic: $subject\n" .
    "Quiz Code: $quizCode\n" .
    "Number of Questions: $numQuestions\n" .
    "Difficulty: $difficulty\n\n" .
    "Use this schema:\n$schema\n\n" .
    "Each question must be relevant to $subject and match the $difficulty level.\n" .
    "Ensure that the correct_ans is randomly distributed across 'A', 'B', 'C', and 'D' instead of always being 'A'.\n" .
    "The SQL should first insert one record into quizzes, then $numQuestions records into questions, referencing the new quiz_id.\n" .
    "Return only raw SQL code without explanation or markdown formatting.";

$apiKey = 'AIzaSyB5zCgvKGQ6NGIiMpj8pNpEPspaQKN_yyU';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent?key=$apiKey",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'contents' => [
            ["parts" => [["text" => $prompt]]]
        ]
    ]),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error: $err");
}

$responseData = json_decode($response, true);
if (!$responseData) {
    die('Failed to parse JSON: ' . json_last_error_msg());
}

// Extract the SQL text
$generatedSQL = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
$generatedSQL = trim(preg_replace(['/^```sql\s*/', '/```$/'], '', $generatedSQL));

if (!$generatedSQL) {
    die('No SQL query returned by Gemini.');
}

// Execute the generated SQL
if ($conn->multi_query($generatedSQL)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());

    header("Location: manage.php");
    exit;
} else {
    die("Database error: " . $conn->error);
}
