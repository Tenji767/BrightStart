<?php
$conn = new mysqli("sql305.infinityfree.com", "if0_41181546", "iloveliberty26!", "if0_41181546_learningdatabase");

$result = $conn->query("SELECT * FROM GRADES");

while ($row = $result->fetch_assoc()) {
    echo "<a href='concepts.php?grade_id={$row['gradeID']}'>";
    echo "<div>{$row['gradeDesc']}</div>";
    echo "</a>";
}
?>


<!-- written by Benjamin Nguyen -->