<?php
session_start();

include("../db_connect.php");

$role = $_SESSION['role'] ?? '';
if(!isset($_SESSION['user_id']) || ($role !== "teacher" && $role !== "admin")){
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<!-- standard head -->
<head>
    <title>BrightStart Control Panel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="tutor-style.css">
</head>

<div class="admin-header">
    <h1 class="pagename">BrightStart Admin Dashboard</h1>
    <h2><a href="tutor-account.php">My Account</a></h2>
</div>
<h2>
    <?php
    if(isset($_SESSION['school_id'])){
    $conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
    if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check for connection 
}
        $stmt = $conn->prepare("SELECT school_name FROM School WHERE school_id=?");//get the school name based off of the school id of the teacher acccessing the dashboard
        $stmt->bind_param("i", $_SESSION['school_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            echo $row['school_name'];//spit out the school name in the h2 header
        }
    }
    ?>
</h2>

<!-- overview of number of lessons and questions in the database -->
<div class="dashboard-stats">
    <div id="lesson-count" class="stat-box">

<?php 

$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}
$stmt = $conn->prepare("SELECT COUNT(lesson_id) FROM Lesson WHERE school_id = ?");
$stmt->bind_param("i", $_SESSION['school_id']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    echo "<p>Total Lessons: </p><h2>" . $row['COUNT(lesson_id)'] . "</h2>";
} else {
    echo "<p>Total Lessons: </p><h2>0</h2>";
}

?>
</div>

<div id="question-count" class="stat-box">
    
<?php
$stmt = $conn->prepare("SELECT COUNT(q.question_id) FROM Questions q JOIN Lesson l ON q.lesson_id = l.lesson_id WHERE l.school_id = ?");
$stmt->bind_param("i", $_SESSION['school_id']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    echo "<p>Total Questions: </p><h2>" . $row['COUNT(q.question_id)'] . "</h2>";
} else {
    echo "<p>Total Questions: </p><h2>0</h2>";
}

?>

</div>
</div>

<div class="dashboard-pages">
    <a href="tutor-lesson-create.php"><button>Create Lessons</button></a>
    <a href="tutor-manage-lessons.php"><button>Manage Lessons</button></a>
    <a href="tutor-create-questions.php"><button>Create Questions</button></a>
    <a href="tutor-student-progress.php"><button>Student Progress</button></a>
</div>


</html>  
<!-- lines 6-7, 21, 59-60, 76-77, 82, 96 written by Caleb McHaney -->
<!-- lines 26 written by Jordan Munster -->
<!-- lines 93-94 written by Reba Ponniah -->
<!-- lines 2, 4, 8-10, 16-20, 22, 24-25, 27, 31-42, 46-47, 65, 67, 84, 95, 99-100 written by Benjamin Nguyen -->
<!-- lines 1, 3, 5, 11-15, 23, 28-30, 43-45, 48-58, 61-64, 66, 68-75, 78-81, 83, 85-92, 97-98 written by Benjamin Nguyen -->
