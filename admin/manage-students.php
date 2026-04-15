<?php
session_start();
include("../db_connect.php");

?>
<!DOCTYPE html>
<html>
<head>
    <title>BrightStart Admin Manage Tutor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h1>Student Management</h1>

    <h3>Add New Student</h3>
    <form action="add-student.php" method="post">
        <label for="student_name">Name:</label>
        <input type="text" id="student_name" name="student_name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="grade">Grade Level:</label>
        <select id="grade_level" name="grade_level" required>
            <option value="">Select grade level</option>
            <option value="1">1st Grade</option>
            <option value="2">2nd Grade</option>
            <option value="3">3rd Grade</option>
            <option value="4">4th Grade</option>
            <option value="5">5th Grade</option>
            <option value="6">6th Grade</option>
            <option value="7">7th Grade</option>
            <option value="8">8th Grade</option>
            <option value="9">9th Grade</option>
            <option value="10">10th Grade</option>
            <option value="11">11th Grade</option>
            <option value="12">12th Grade</option>
        </select>

        <label for="school">School:</label>
        <select id="school_id" name="school_id" required>
            <option value="">Select a school</option>
            <?php
            $stmt = $conn->prepare("SELECT school_id, school_name FROM School");
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['school_id'] . "'>" . $row['school_name'] . "</option>";
                }
            }
            ?>


        </select>
        <br><br>
        <button type="submit">Add Student</button>
    </form>
<br><hr><br>


<table id="Student-list">


<tr>
    <th>Student ID</th>
    <th>Student Name</th>
    <th>Grade Level</th>
    <th>School Affiliation</th>
    <th>Email</th>
    <th></th>
</tr>
<?php

$stmt = $conn->prepare("SELECT student_id, student_name, grade_id, StudentAccount.school_id, email, school_name FROM StudentAccount JOIN School ON StudentAccount.school_id = School.school_id");

$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>" . $row['student_id'] . "</td>";
        echo "<td>" . $row['student_name'] . "</td>";
        echo "<td>" . $row['grade_id'] . "</td>";
        echo "<td>" . $row['school_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td><a href='edit-student.php?student_id=" . $row['student_id'] . "'><button>&#9881</button></a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No students found</td></tr>";
}

?>





</table>
</body>




</html>

