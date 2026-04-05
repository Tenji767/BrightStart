<?php
error_reporting(E_ALL);//display errors upon starting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>
<!DOCTYPE HTML>
<!-- standard head -->
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Lesson Management</title>
<link rel="stylesheet" href="admin-style.css">
</head>

<body>



<div class="admin-header">
<h1 class="pagename">Manage Lessons</h1>
</div>

<div class="returnBox">
<a href="admin-dashboard(notAI).php" class="returnBtn">To Dashboard</a>


</div>

<table>
    <tr>
        <th>Lesson Title</th>
        <th>Lesson ID</th>
        <th>Grade</th>
        <th>Teacher</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
<?php


$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

}

$stmt = $conn->prepare("SELECT lesson_title, lesson_id, grade_id, teacher_name FROM Lesson LEFT JOIN TeacherAccount ON Lesson.teacher_id = TeacherAccount.teacher_id;
");

$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['lesson_title'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['lesson_id'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['grade_id'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['teacher_name'] ?? '') . "</td>";
    // Add a link to a copy of the create lesson page, but have it save the lesson the same way while also deleting the old lesson. Make sure to transfer/upload the old lesson too
    //add a delete button that asks for confirmation before deleting the lesson from the table
    echo "<td>Edit Lesson</td>";
    echo "<td>
    <form method=\"POST\" action=\"delete-lesson.php\" onsubmit=\"return confirm('Delete this lesson?');\">
            <input type=\"hidden\" name=\"lesson_id\" value=\"" . htmlspecialchars($row['lesson_id'] ?? '') . "\">
            <button type=\"submit\">Delete</button>
        </form></td>";
    echo "</tr>";
}
?>



</table>





</body>
</html>