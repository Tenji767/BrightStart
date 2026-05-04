<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];

    $stmt = $conn->prepare("DELETE FROM StudentAccount WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    header("Location: manage-students.php");
    exit();
}
// lines 1-22 written by Caleb McHaney
?>
