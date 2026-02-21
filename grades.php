<?php
echo"loaded";
$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P!", "if0_41201125_brightstar_db");

$result = $conn->query("SELECT * FROM Grades");
if (!$result) {
    die("Query failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    echo "<a href='concepts.php?grade_id={$row['gradeID']}'>";
    echo "<div>{$row['gradeDesc']}</div>";
    echo "</a>";
}
?>


<!-- written by Benjamin Nguyen -->