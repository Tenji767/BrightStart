<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="lessons.css">

</head>

<?php include('includes/nav.php');?>
<body>

<?php
session_start();
include('includes/header.php');
include('includes/nav.php');//include all the header and navs


$grade = $_GET['grade_id'];//gets the grade and concept from url
$concept = $_GET['concept_id'];
$school_id = intval($_SESSION['school_id'] ?? 0);




$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection


}



$stmt = $conn->prepare("SELECT * FROM Lesson WHERE grade_id = ? AND lesson_id = ? AND school_id = ?");//gets the lesson matching grade, lesson id, and school
$stmt->bind_param("iii", $grade, $concept, $school_id);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();
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