<?php
// session_start();

// include("../db_connect.php");

// if(!isset($_SESSION['user_id']) && $_SESSION['role'] != "teacher"){
//     header("Location: ../login.php");
//     exit();
// }

?>

<!DOCTYPE html>
<html>

<?php include("../header.php"); ?>

<button onclick="window.location.href='admin.php'">Back to Dashboard</button>
<h1>Admin Dashboard</h1>

<div class="dashboard-stats">
    <div id="lesson-count" class="stat-box">

<?php 

$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}
$stmt = $conn->prepare("SELECT COUNT(lesson_id) FROM Lesson");
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    echo "<p>Total Lessons: " . $row['COUNT(lesson_id)'] . "</p>";
} else {
    echo "<p>Total Lessons: 0</p>";
}

?>
</div>

<div id="question-count" class="stat-box">
    
<?php
$stmt = $conn->prepare("SELECT COUNT(question_id) FROM Questions");
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    echo "<p>Total Questions: " . $row['COUNT(question_id)'] . "</p>";
} else {
    echo "<p>Total Questions: 0</p>";
}

?>

</div>
</div>

<div class="dashboard-pages">
    <a href="admin-lesson-create(textonly).php"><button>Create Lessons</button></a>
    <a href="admin-create-questions.php"><button>Create Questions</button></a>
</div>

</html>