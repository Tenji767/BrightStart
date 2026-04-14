<?php
session_start();
include "db_connect.php";

$email = $_POST['email'];
$password = $_POST['password'];

/* CHECK TEACHER */

$sql = "SELECT * FROM TeacherAccount WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $teacher = $result->fetch_assoc();

    if(password_verify($password,$teacher['password_hash'])){

        $_SESSION['user_id'] = $teacher['teacher_id'];
        $_SESSION['role'] = "teacher";
        $_SESSION['teacher_name'] = $teacher['teacher_name'];
        $_SESSION['school_id'] = $teacher['school_id'];
        $_SESSION['email'] = $teacher['email'];

        header("Location: tutor/tutor-dashboard(notAI).php");
        exit();
    }
}

/* CHECK STUDENT */

$sql = "SELECT * FROM StudentAccount WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $student = $result->fetch_assoc();

    if(password_verify($password,$student['password_hash'])){

        $_SESSION['user_id'] = $student['student_id'];
        $_SESSION['role'] = "student";
        $_SESSION['student_name'] = $student['student_name'];
        $_SESSION['grade'] = $student['grade_id'];
        $_SESSION['email'] = $student['email'];
        $_SESSION['school_id'] = $student['school_id'];
        header("Location: index.php");
        exit();
    }
}

echo "Invalid email or password.";
echo "<br><a href='login.php'>Try again</a>";
?>