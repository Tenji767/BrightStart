<?php
include('includes/header.php');
include('includes/nav.php');//include all the header and navs


$grade = $_GET['grade_id'];//gets the grade and concept from url
$concept = $_GET['concept_id'];




$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}



$stmt = $conn->prepare("SELECT * FROM Lesson where grade_id=? and lesson_id=?");//gets the lesson that corresponds to the grade and lesson id selected
$stmt->bind_param("ii", $grade, $concept);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

// Block access if the lesson has been disabled by an admin
if (!$row || (isset($row['is_enabled']) && (int)$row['is_enabled'] === 0)) {
    echo "<a href='concepts.php?grade_id={$grade}'><div><p>Select Lesson</p></div></a>";
    echo "<p>This lesson is not currently available.</p>";
    exit;
}

//a button to go back to the lesssons select page for convenience
echo "<a href='concepts.php?grade_id={$grade}'>";
echo "<div><p>Select Lesson</p></div>";
echo "</a>";
//displays the lesson title pulled from the database and regurgitates the lesson content html, which displays it as content
echo "<h1>" . $row['lesson_title'] . "</h1>";
echo "<div id='lesson-content'>";
echo $row['lesson_content_html'];
echo "</div>";
?>
<!-- lines 1-36 written by Benjamin Nguyen -->