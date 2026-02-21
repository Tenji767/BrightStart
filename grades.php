<?php
echo"<p>loaded</p>";
$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P!", "if0_41201125_brightstar_db");//log in

$result = $conn->query("SELECT * FROM Grades");//gets all the grades
if (!$result) {
    die("Query failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {//displays all the grade names
    echo "<a href='concepts.php?grade_id={$row['gradeID']}'>";
    echo "<div><p>{$row['gradeDesc']}</p></div>";
    echo "</a>";
}
?>


<!-- lines 1-15 written by Benjamin Nguyen -->