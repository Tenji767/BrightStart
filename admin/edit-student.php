<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../db_connect.php");

$student_id = $_POST['student_id'] ?? $_GET['student_id'];
if (!$student_id) {
    echo "<script>alert('No student ID provided.')</script>";
    header("Location: manage-students.php");
    exit();
}

$stmt = $conn->prepare("SELECT student_id, student_name, grade_id, StudentAccount.school_id, email, school_name FROM StudentAccount JOIN School ON StudentAccount.school_id = School.school_id WHERE student_id = ?");
$stmt->bind_param("i", $student_id);

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    echo "<script>alert('Student not found.')</script>";
    header("Location: manage-students.php");
    exit();
}

$student_name = $row['student_name'];
$grade_id = $row['grade_id'];
$school_id = $row['school_id'];
$school_name = $row['school_name'];
$email = $row['email'];

//AY don't touch anything, above are the variables that are going to be used as default values in the form that can be edited, and below is the code that takes whatever is in the form and updates the database with those new forms. Should technically work the same as edit-school.php. Remember to use a select dropdown for the school affiliation, with the default value being the current school, and use the school ids as values and school names as the text.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $grade_id = $_POST['grade_id'];
    $email = $_POST['email'];
    $school_id = $_POST['school_id'];

    $stmt = $conn->prepare("UPDATE StudentAccount SET student_name = ?, grade_id = ?, email = ?, school_id = ? WHERE student_id = ?");
    $stmt->bind_param("sisii", $student_name, $grade_id, $email, $school_id, $student_id);

    if ($stmt->execute()) {
        header("Location: manage-students.php");
        echo "<script>alert('Student updated successfully.')</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Student</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

    <form action="edit-student.php?student_id=<?php echo urlencode($student_id); ?>" method="post">

        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">

        <label for="student_name">Student Name:</label><br>
        <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars($student_name); ?>" required><br><br>

        <label for="email"> Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>

        <label for="school_id">Affiliated School:</label><br>
        <select id="school_id" name="school_id" required>
            <option value="<?php echo htmlspecialchars($school_id); ?>" selected><?php echo htmlspecialchars($school_name); ?></option>
            <?php
            $stmt = $conn->prepare("SELECT school_id, school_name FROM School");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['school_id']) . "'>" . htmlspecialchars($row['school_name']) . "</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" value="Save Changes">
    </form>


    <form action="delete-student.php" method="post" onsubmit="return doubleConfirm()">
        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
        <button type="submit">Delete Student</button>
    </form>
<script>
    function doubleConfirm() {
        if (confirm("Are you sure you want to delete this student? This cannot be undone.")) {
            return confirm("Are you absolutely sure? Consult your IT person before pressing this button. This will delete the student.")
        }
    }
</script>

</body>

<?php 


?>



</html>
<!-- lines 3-6 written by Caleb McHaney -->
<!-- lines 1-2, 7-117 written by Benjamin Nguyen -->
