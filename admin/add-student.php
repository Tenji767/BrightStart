<?php
include("../db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $email = $_POST['email'];
    $school_id = $_POST['school_id'];
    $password_hash = password_hash("brightstart222", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO StudentAccount (student_name, email, school_id, password_hash) VALUES (?, ?, ?, '$password_hash')");
    $stmt->bind_param("ssi", $student_name, $email, $school_id);

    if ($stmt->execute()) {
        header("Location: manage-students.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}