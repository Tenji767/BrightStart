<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_name = $_POST['school_name'];
    $student_join_code = $_POST['student_join_code'];
    $teacher_join_code = $_POST['teacher_join_code'];

    $stmt = $conn->prepare("INSERT INTO School (school_name, student_join_code, teacher_join_code) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $school_name, $student_join_code, $teacher_join_code);

    if ($stmt->execute()) {
        header("Location: manage-schools.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
// lines 2-6 written by Caleb McHaney
// lines 1, 7-23 written by Benjamin Nguyen
