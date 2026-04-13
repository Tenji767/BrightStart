<?php
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