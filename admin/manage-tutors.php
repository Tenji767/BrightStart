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
    <h1>Tutor Management</h1>

    <h3>Add New Tutor</h3>
    <form action="add-tutor.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="school">School:</label>
        <select id="school" name="school" required>
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
        <button type="submit">Add Tutor</button>
    </form>
<br><hr><br>


<table id="tutor-list">


<tr>
    <th>Tutor ID</th>
    <th>Tutor Name</th>
    <th>School Affiliation</th>
    <th>Email</th>
    <th></th>
</tr>
<?php

$stmt = $conn->prepare("SELECT teacher_id, teacher_name, TeacherAccount.school_id, email, school_name FROM TeacherAccount JOIN School ON TeacherAccount.school_id = School.school_id");

$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>" . $row['teacher_id'] . "</td>";
        echo "<td>" . $row['teacher_name'] . "</td>";
        echo "<td>" . $row['school_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td><a href='edit-tutor.php?tutor=" . $row['teacher_id'] . "'><button>&#9881</button></a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No tutors found</td></tr>";
}

?>





</table>
</body>




</html>

