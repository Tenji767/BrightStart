<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$title = $_POST['title'] ?? '';
$grade = $_POST['grade'] ?? '';
$html = $_POST['html'] ?? '';

$uploadDir = "uploads/";

// DEBUG FILES
echo "<pre>";
print_r($_FILES);
echo "</pre>";

foreach($_FILES as $file){
    $path = $uploadDir . basename($file["name"]);

    if(move_uploaded_file($file["tmp_name"], $path)){
        echo "Uploaded: " . $file["name"] . "<br>";
    } else {
        echo "Failed: " . $file["name"] . "<br>";
    }
}

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

echo "Lesson saved";
?>