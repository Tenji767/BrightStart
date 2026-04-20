<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
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
    <a href="admin-dashboard.php" class="home-btn">Home</a>
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

        <button type="submit">Add School</button> 
    </form>

    <br>
    <hr>
    <br>
<table id="school-list">
<tr>
    <th>School ID</th>
    <th>School Name</th>
    <th>Student Join Code</th>
    <th>Teacher Join Code</th>
    <th></th>
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
        echo "<td><a href='edit-school.php?school_id=" . $row['school_id'] . "'><button>&#9881</button></a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No schools found</td></tr>";
}

?>

</table>

<style>
/*general*/
    .header {
  display: flex;
  align-items: center;
  padding: 25px 40px;
}

h1 {
  font-size: 36px;
  color: #1e293b;
  text-align: left;
}
    /* Home button */
.home-btn {
  background: #2563eb;
  color: white;
  border: none;
  top: 10px;
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.home-btn:hover {
  background: #1d4ed8;
}

</style>

</body>




</html>

