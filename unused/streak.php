<!-- please do not touch this file, testing something - BN -->

<?php 

$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}

$stmt = $conn->prepare("SELECT * FROM StudentAccount WHERE student_id=?");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();





?>
