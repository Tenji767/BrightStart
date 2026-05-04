<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_id = $_POST['school_id'];

    //DELETION, BEWARE YE
    $stmt = $conn->prepare("DELETE FROM School WHERE school_id = ?");
    $stmt->bind_param("i", $school_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM Lesson WHERE school_id = ?");
    $stmt->bind_param("i", $school_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM StudentAccount WHERE school_id = ?");
    $stmt->bind_param("i", $school_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM TeacherAccount WHERE school_id = ?");
    $stmt->bind_param("i", $school_id);
    $stmt->execute();

    header("Location: manage-schools.php");
    exit();

}

?>
// lines 2-6 written by Caleb McHaney
// lines 1, 7-35 written by Benjamin Nguyen
