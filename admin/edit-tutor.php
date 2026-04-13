<?php
session_start();
include("../db_connect.php");

$tutor_id = $_POST['tutor_id'] ?? $_GET['tutor_id'];
if (!$tutor_id) {
    echo "<script>alert('No tutor ID provided.')</script>";
    header("Location: manage-tutors.php");
    exit();
}

$stmt = $conn->prepare("SELECT tutor_id, tutor_name, school_id, email, school_name FROM TeacherAccount JOIN School ON TeacherAccount.school_id = School.school_id WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    echo "<script>alert('Tutor not found.')</script>";
    header("Location: manage-tutors.php");
    exit();
}

$tutor_name = $row['tutor_name'];
$school_id = $row['school_id'];
$email = $row['email'];

//AY don't touch anything, above are the variables that are going to be used as default values in the form that can be edited, and below is the code that takes whatever is in the form and updates the database with those new forms. Should technically work the same as edit-school.php. Remember to use a select dropdown for the school affiliation, with the default value being the current school, and use the school ids as values and school names as the text.

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
            return confirm("Are you absolutely sure? Consult your IT person before pressing this button. This will delete everythin related to the school.")
        }
    }
</script>

</body>

<?php 


?>



</html>