<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../db_connect.php");

$school_id = $_POST['school_id'] ?? $_GET['school_id'];
if (!$school_id) {
    echo "<script>alert('No school ID provided.')</script>";
    header("Location: manage-schools.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM School WHERE school_id = ?");
$stmt->bind_param("i", $school_id);

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    echo "<script>alert('School not found.')</script>";
    header("Location: manage-schools.php");
    exit();
}

$school_name = $row['school_name'];
$student_join_code = $row['student_join_code'];
$teacher_join_code = $row['teacher_join_code'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_name = $_POST['school_name'];
    $student_join_code = $_POST['student_join_code'];
    $teacher_join_code = $_POST['teacher_join_code'];

    $stmt = $conn->prepare("UPDATE School SET school_name = ?, student_join_code = ?, teacher_join_code = ? WHERE school_id = ?");
    $stmt->bind_param("sssi", $school_name, $student_join_code, $teacher_join_code, $school_id);

    if ($stmt->execute()) {
        header("Location: manage-schools.php");
        echo "<script>alert('School updated successfully.')</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit School</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

    <form action="edit-school.php?school_id=<?php echo urlencode($school_id); ?>" method="post">

        <input type="hidden" name="school_id" value="<?php echo htmlspecialchars($school_id); ?>">

        <label for="school_name">School Name:</label><br>
        <input type="text" id="school_name" name="school_name" value="<?php echo htmlspecialchars($school_name); ?>" required><br><br>

        <label for="student_join_code">Student Join Code:</label><br>
        <input type="text" id="student_join_code" name="student_join_code" value="<?php echo htmlspecialchars($student_join_code); ?>" required><br><br>

        <label for="teacher_join_code">Teacher Join Code:</label><br>
        <input type="text" id="teacher_join_code" name="teacher_join_code" value="<?php echo htmlspecialchars($teacher_join_code); ?>" required><br><br>

        <input type="submit" value="Save Changes">
    </form>


    <form action="delete-school.php" method="post" onsubmit="return doubleConfirm()">
        <input type="hidden" name="school_id" value="<?php echo htmlspecialchars($school_id); ?>">
        <button type="submit">Delete School</button>
    </form>
<script>
    function doubleConfirm() {
        if (confirm("Are you sure you want to delete this school? This will permanently delete all associated lesson data and student/teacher accounts and cannot be undone.")) {
            return confirm("Are you absolutely sure? Consult your IT person before pressing this button. This will delete everything related to the school.")
        }
    }
</script>

</body>

<?php 


?>



</html>