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
        <th>Status</th>
        <!-- <th>Edit</th> -->
        <th>Delete</th>
    </tr>
<?php


$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

}

$stmt = $conn->prepare("SELECT lesson_title, lesson_id, grade_id, teacher_name, is_enabled FROM Lesson LEFT JOIN TeacherAccount ON Lesson.teacher_id = TeacherAccount.teacher_id;
");

$stmt->execute();
$result = $stmt->get_result();

// lesson toggling buttons and status indicators
while($row = $result->fetch_assoc()) {
    $isEnabled = (int)($row['is_enabled'] ?? 1);
    $statusLabel = $isEnabled ? "Enabled" : "Disabled";
    $statusClass = $isEnabled ? "status-enabled" : "status-disabled";
    $toggleLabel = $isEnabled ? "Disable" : "Enable";
    $toggleClass = $isEnabled ? "toggle-btn disable-btn" : "toggle-btn enable-btn";

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['lesson_title'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['lesson_id'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['grade_id'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['teacher_name'] ?? '') . "</td>";
    echo "<td>
        <span class=\"" . $statusClass . "\">" . $statusLabel . "</span>
        <form method=\"POST\" action=\"toggle-lesson.php\">
            <input type=\"hidden\" name=\"lesson_id\" value=\"" . htmlspecialchars($row['lesson_id'] ?? '') . "\">
            <input type=\"hidden\" name=\"is_enabled\" value=\"" . $isEnabled . "\">
            <button type=\"submit\" class=\"" . $toggleClass . "\">" . $toggleLabel . "</button>
        </form>
    </td>";
    // Add a link to a copy of the create lesson page, but have it save the lesson the same way while also deleting the old lesson. Make sure to transfer/upload the old lesson too
    //add a delete button that asks for confirmation before deleting the lesson from the table
    // echo "<td><a href=\"admin-lesson-create.php?lesson_id=" . htmlspecialchars($row['lesson_id']) . "\">Edit</a></td>";
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