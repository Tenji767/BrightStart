<?php
$conn = new mysqli("localhost", "username", "password", "database");

$result = $conn->query("SELECT * FROM GRADES");

while ($row = $result->fetch_assoc()) {
    echo "<a href='concepts.php?grade_id={$row['id']}'>";
    echo "<div>{$row['description']}</div>";
    echo "</a>";
}
?>
