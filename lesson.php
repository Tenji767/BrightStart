<?php
include('includes/header.php');
include('includes/nav.php');


$grade = $_GET['grade_id'];
$concept = $_GET['concept_id'];




$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}



$stmt = $conn->prepare("SELECT * FROM Lesson where grade_id=? and lesson_id=?");
$stmt->bind_param("ii", $grade, $concept);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

echo "<a href='concepts.php?grade_id={$grade}'>";
echo "<div><p>Select Lesson</p></div>";
echo "</a>";

echo "<h1>" . $row['lesson_title'] . "</h1>";
echo "<div id='lesson-content'>";
echo $row['lesson_content_html'];
echo "</div>";
?>