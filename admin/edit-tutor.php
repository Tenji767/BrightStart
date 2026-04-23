<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check for connection

  
}
$teacher_id = $_POST['teacher_id'] ?? $_GET['teacher_id'];
if (!$teacher_id) {
    echo "<script>alert('No teacher ID provided.')</script>";
    header("Location: manage-tutors.php");
    exit();
}

$stmt = $conn->prepare("SELECT teacher_id, teacher_name, TeacherAccount.school_id, email, school_name FROM TeacherAccount JOIN School ON TeacherAccount.school_id = School.school_id WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    echo "<script>alert('Teacher not found.')</script>";
    header("Location: manage-tutors.php");
    exit();
}

$teacher_name = $row['teacher_name'];
$school_id = $row['school_id'];
$school_name = $row['school_name'];
$email = $row['email'];

//AY don't touch anything, above are the variables that are going to be used as default values in the form that can be edited, and below is the code that takes whatever is in the form and updates the database with those new forms. Should technically work the same as edit-school.php. Remember to use a select dropdown for the school affiliation, with the default value being the current school, and use the school ids as values and school names as the text.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_name = $_POST['teacher_name'];
    $email = $_POST['email'];
    $school_id = $_POST['school_id'];

    $stmt = $conn->prepare("UPDATE TeacherAccount SET teacher_name = ?, email = ?, school_id = ? WHERE teacher_id = ?");
    $stmt->bind_param("ssii", $teacher_name, $email, $school_id, $teacher_id);

    if ($stmt->execute()) {
        header("Location: manage-tutors.php");
        echo "<script>alert('Teacher updated successfully.')</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Tutor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

    <form action="edit-tutor.php?teacher_id=<?php echo urlencode($teacher_id); ?>" method="post">

        <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacher_id); ?>">

        <label for="teacher_name">Teacher Name:</label><br>
        <input type="text" id="teacher_name" name="teacher_name" value="<?php echo htmlspecialchars($teacher_name); ?>" required><br><br>

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


    <form action="delete-tutor.php" method="post" onsubmit="return doubleConfirm()">
        <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacher_id); ?>">
        <button type="submit">Delete Tutor</button>
    </form>
<script>
    function doubleConfirm() {
        if (confirm("Are you sure you want to delete this tutor? This cannot be undone.")) {
            return confirm("Are you absolutely sure? Consult your IT person before pressing this button. This will delete the tutor and leave all of their lessons with a non-existent tutor.")
        }
    }
</script>

</body>



</html>