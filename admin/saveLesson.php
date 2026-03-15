<?php

$conn = new mysqli("sql112.infinityfree.com","DB_USER","DB_PASS","DB_NAME");

$title = $_POST['title'];
$grade = $_POST['grade'];
$html = $_POST['html'];

$uploadDir = "uploads/";

// save uploaded images
foreach($_FILES as $file){

$path = $uploadDir . basename($file["name"]);

move_uploaded_file($file["tmp_name"], $path);

}

// save lesson
$stmt = $conn->prepare(
"INSERT INTO Lesson (lesson_title, grade_id, lesson_content_html)
VALUES (?, ?, ?)"
);

$stmt->bind_param("sis", $title, $grade, $html);

$stmt->execute();

echo "Lesson saved";

?>