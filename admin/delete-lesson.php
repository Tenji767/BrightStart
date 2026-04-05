<?php

$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}

if (isset($_POST['lesson_id'])) {
    $id = intval($_POST['lesson_id']);

    $stmt = $conn->prepare("DELETE FROM Lesson WHERE lesson_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// redirect back
header("Location: manage-lessons.php");
exit;