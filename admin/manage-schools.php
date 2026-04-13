<?php
session_start();
include("../db_connect.php");

?>
<!DOCTYPE html>
<html>
<head>
    <title>BrightStart Admin Manage Schools</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h1>Schools</h1>
    <p>Manage participating schools</p>


    <h3>Add New School</h3>
    <form action="add-school.php" method="post">
        <label for="school_name">School Name:</label><br>
        <input type="text" id="school_name" name="school_name" required><br><br>

        <label for="student_join_code">Student Join Code:</label><br>
        <input type="text" id="student_join_code" name="student_join_code" required><br><br>

        <label for="teacher_join_code">Teacher Join Code:</label><br>
        <input type="text" id="teacher_join_code" name="teacher_join_code" required><br><br>

        <input type="submit" value="Add School">    
    </form>

<table id="school-list">
<tr>
    <th>School ID</th>
    <th>School Name</th>
    <th>Student Join Code</th>
    <th>Teacher Join Code</th>
</tr>
<?php

$stmt = $conn->prepare("SELECT * FROM School");

$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>" . $row['school_id'] . "</td>";
        echo "<td>" . $row['school_name'] . "</td>";
        echo "<td>" . $row['student_join_code'] . "</td>";
        echo "<td>" . $row['teacher_join_code'] . "</td>";
        echo "<td><a href='edit-school.php?school_id=" . $row['school_id'] . "'><button>Edit</button></a></td>";
        echo "<td><a href='delete-school.php?school_id=" . $row['school_id'] . "'><button>Delete</button></a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No schools found</td></tr>";
}

?>

</table>

</body>




</html>

