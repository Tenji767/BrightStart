<?php
$conn = new mysqli("localhost", "username", "password", "database");

$grade_id = $_GET['grade_id'];

$stmt = $conn->prepare("SELECT * FROM CONCEPTS WHERE gradeID = ?");
$stmt->bind_param("i", $grade_id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo $row['conceptContent'];
    echo "</div>";
}
?>
