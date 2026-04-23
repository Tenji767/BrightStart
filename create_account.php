<?php
// Error reporting is turned on for debugging purposes. Remove or comment out these lines in production.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include "db_connect.php";

// $type = $_POST['account_type'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$join_code = $_POST['join_code'];
$grade_id = $_POST['grade_id'];

/* Find school using join code */

$schoolQuery = $conn->query("
SELECT * FROM School
WHERE student_join_code='$join_code'
OR teacher_join_code='$join_code'
");

if($schoolQuery->num_rows == 0){
    die("Invalid join code.");
}

$school = $schoolQuery->fetch_assoc();
$school_id = $school['school_id'];


/* Create Student */

// if($type == "student"){

    if($join_code != $school['student_join_code']){
        die("Invalid student join code.");
    }

    $sql = "INSERT INTO StudentAccount
    (school_id, grade_id, student_name, email, password_hash)
    VALUES
    ('$school_id','$grade_id','$name','$email','$password')";
    
// }


// /* Create Teacher */

// if($type == "teacher"){

//     if($join_code != $school['teacher_join_code']){
//         die("Invalid teacher join code.");
//     }

//     $sql = "INSERT INTO TeacherAccount
//     (school_id, teacher_name, email, password_hash)
//     VALUES
//     ('$school_id','$name','$email','$password')";
   
// }

$stmt = $conn->prepare("SELECT id FROM StudentAccount WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("An account with this email already exists.");
}

if($conn->query($sql)){
    echo "Account created successfully!";
    header("Location: login.php");
}else{
    echo "Error: " . $conn->error;
}
?>