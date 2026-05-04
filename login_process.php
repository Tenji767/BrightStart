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
        $_SESSION['profile_picture'] = $teacher['profile_picture'] ?? 'pfp.png';

        header("Location: tutor/tutor-dashboard.php");
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
        $_SESSION['profile_picture'] = $student['profile_picture'] ?? 'pfp.png';

        $school_stmt = $conn->prepare("SELECT school_name FROM School WHERE school_id = ?");
        $school_stmt->bind_param("i", $student['school_id']);
        $school_stmt->execute();
        $school_row = $school_stmt->get_result()->fetch_assoc();
        $_SESSION['school'] = $school_row['school_name'] ?? '';

        header("Location: index.php");
        exit();
    }
}

/* CHECK admin account */

$sql = "SELECT * FROM AdminAccount WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $admin = $result->fetch_assoc();

    if(password_verify($password,$admin['password_hash'])){

        $_SESSION['user_id'] = $admin['admin_id'];
        $_SESSION['role'] = "admin";
        $_SESSION['admin_name'] = $admin['admin_name'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['school_id'] = $admin['school_id'];
        header("Location: admin/admin-dashboard.php");
        exit();
    }
}

echo "Invalid email or password.";
echo "<br><a href='login.php'>Try again</a>";
?>