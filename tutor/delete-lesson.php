<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection
  
}

if (isset($_POST['lesson_id'])) {
    $id = intval($_POST['lesson_id']);

    $stmt = $conn->prepare("DELETE FROM Lesson WHERE lesson_id = ?");

    if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
}

// redirect back
header("Location: tutor-manage-lessons.php");
exit;
