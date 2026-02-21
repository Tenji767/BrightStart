<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);//development error checking
?>


<!DOCTYPE html>
<html>


<?php

echo "<p>Loaded</p>";
$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection
}
else {
    echo "<p>Login successful</p>";
}

if (!isset($_GET['grade_id'])) {//if accessed outside of from practice page, ereturns error
    die("Grade not specified.");
}

$grade_id = $_GET['grade_id'];//gets the grade that was selected into a variable

$stmt = $conn->prepare("SELECT * FROM Concepts WHERE gradeID = ?");//loads that grade_id variable into the query to get all related concepts
$stmt->bind_param("i", $grade_id);
$stmt->execute();

$result = $stmt->get_result();//puts results intovariable

if(!$result){
    echo "<p>Error loading in concepts</p>";//welp...hopefully you have concepts in the database
}


while ($row = $result->fetch_assoc()) {//puts the results into an array that can be referenced by the name of the column
    $grade = isset($_GET['grade_id']) ? urlencode($_GET['grade_id']) : '';//gets the grade id
    $concept = urlencode($row['conceptID']);//gets the concept id

    echo "<div>";
    echo "<a href='quizes.php?grade_id=$grade&concept_id=$concept'><p>" . htmlspecialchars($row['conceptDesc']) . "</p></a>";//will open the quiz section that will pull questions templates from the database
    echo "</div>";

}
?>
</html>
<!-- lines 1-50 written by Benjamin Nguyen -->