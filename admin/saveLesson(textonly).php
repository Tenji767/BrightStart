<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB CONNECTION
$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// GET DATA
$title = $_POST['title'] ?? '';
$grade = $_POST['grade'] ?? '';
$html  = $_POST['html'] ?? '';

// DEBUG (optional)
echo "Received:<br>";
echo "Title: $title <br>";
echo "Grade: $grade <br>";
echo "HTML: $html <br><hr>";

// SAVE TO DATABASE
$stmt = $conn->prepare(
    "INSERT INTO Lesson (lesson_title, grade_id, lesson_content_html)
     VALUES (?, ?, ?)"
);

if(!$stmt){
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sis", $title, $grade, $html);

if(!$stmt->execute()){
    die("Execute failed: " . $stmt->error);
}

echo "Lesson saved successfully!";
?>