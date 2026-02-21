<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>


<!DOCTYPE html>
<html>


<?php

echo "<p>Loaded</p>";
$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
else {
    echo "<p>Login successful</p>";
}

if (!isset($_GET['grade_id'])) {
    die("Grade not specified.");
}

$grade_id = $_GET['grade_id'];

$stmt = $conn->prepare("SELECT * FROM Concepts WHERE gradeID = ?");
$stmt->bind_param("i", $grade_id);
$stmt->execute();

$result = $stmt->get_result();

if(!$result){
    echo "<p>Error loading in concepts</p>";
}


while ($row = $result->fetch_assoc()) {
    $grade = isset($_GET['grade_id']) ? urlencode($_GET['grade_id']) : '';
    $concept = urlencode($row['conceptID']);

    echo "<div>";
    echo "<a href='quizes.php?grade_id=$grade&concept_id=$concept'><p>" . htmlspecialchars($row['conceptDesc']) . "</p></a>";
    echo "</div>";

}
?>
</html>
<!-- written by Benjamin Nguyen -->