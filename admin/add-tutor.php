<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_name = $_POST['teacher_name'];
    $email = $_POST['email'];
    $school_id = $_POST['school_id'];
    $password_hash = password_hash("defaultpassword4593", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO TeacherAccount (teacher_name, email, school_id, password_hash) VALUES (?, ?, ?, '$password_hash')");
    $stmt->bind_param("ssi", $teacher_name, $email, $school_id);

    if ($stmt->execute()) {
        header("Location: manage-tutors.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
// File written by Benjamin N