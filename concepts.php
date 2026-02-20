<?php
$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P!", "if0_41201125_brightstar_db");

if (!isset($_GET['grade_id'])) {
    die("Grade not specified.");
}

$grade_id = $_GET['grade_id'];

$stmt = $conn->prepare("SELECT * FROM CONCEPTS WHERE gradeID = ?");
$stmt->bind_param("i", $grade_id);
$stmt->execute();

$result = $stmt->get_result();




while ($row = $result->fetch_assoc()) {
    $grade = isset($_GET['grade_id']) ? urlencode($_GET['grade_id']) : '';
    $concept = urlencode($row['conceptID']);

    echo "<div>";
    echo "<a href='quizes.php?grade_id=$grade&concept_id=$concept'><p>" . htmlspecialchars($row['conceptName']) . "</p></a>";
    echo "</div>";

}
?>

<!-- written by Benjamin Nguyen -->