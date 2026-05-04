<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_POST['teacher_id'];

    //DELETION, BEWARE YE
    $stmt = $conn->prepare("DELETE FROM TeacherAccount WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();

    header("Location: manage-tutors.php");
    exit();

}

?>
<!-- File written by Benjamin N -->