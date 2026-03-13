<?php

$data = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");

$title = $data['title'];
$grade = $data['grade'];
$html = $data['html'];

$stmt = $conn->prepare("INSERT INTO Lesson (lesson_title, grade_id, lesson_content_html) VALUES (?, ?, ?)");

$stmt->bind_param("sis", $title, $grade, $html);

$stmt->execute();

echo "Lesson saved";

?>