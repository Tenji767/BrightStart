<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB CONNECTION
$conn = new mysqli(
    "sql112.infinityfree.com",
    "if0_41201125",
    "EvKOulpa615P",
    "if0_41201125_brightstar_db"
);

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