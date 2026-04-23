<?php
session_start();
$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'teacher' && $role !== 'admin')) {
    header("Location: ../login.php");
    exit();
}
error_reporting(E_ALL);//error reporting
ini_set('display_errors', 1);

$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

//db connect
}


$title = $_POST['title'] ?? '';//gets the stuff from the form (submitted from the create lesson)
$grade = $_POST['grade'] ?? '';
$html = $_POST['html'] ?? '';
$school_id = intval($_SESSION['school_id'] ?? 0);

if (!empty($_FILES)) {
    foreach ($_FILES as $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo "Upload error for " . $file['name'] . "<br>";
            continue;
        }
        // Optional: Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Invalid file type for " . $file['name'] . "<br>";
            continue;
        }
    }
}//this if block was recommended by copilot

$uploadDir = "uploads/";//pulls images from the uploads folder

// Check if uploads directory exists and is writable
if (!file_exists($uploadDir)) {
    echo "Uploads directory does not exist.<br>";
    exit;
}
if (!is_writable($uploadDir)) {
    echo "Uploads directory is not writable.<br>";
    exit;
}

// DEBUG FILES
echo "<pre>";
print_r($_FILES);
echo "</pre>";

foreach($_FILES as $file){//goes through each file and uploads it (files are images and diagrams)
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Upload error for " . $file['name'] . ": " . $file['error'] . "<br>";
        continue;
    }
    // Optional: Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo "Invalid file type for " . $file['name'] . "<br>";
        continue;
    }
    $path = $uploadDir . basename($file["name"]);

    if(move_uploaded_file($file["tmp_name"], $path)){//message based on file upload success
        echo "Uploaded: " . $file["name"] . "<br>";
    } else {
        echo "Failed to move: " . $file["name"] . "<br>";
    }
}

$stmt = $conn->prepare(//inserts the lesson title, grade, html content, and school into database
"INSERT INTO Lesson (lesson_title, grade_id, lesson_content_html, school_id, teacher_id)
VALUES (?, ?, ?, ?, ?)"
);

if(!$stmt){
    die("Prepare failed: " . $conn->error);
}//failure statement

$stmt->bind_param("sisi", $title, $grade, $html, $school_id, $_SESSION['user_id']);//binds the actual values to the statement

if(!$stmt->execute()){//runs statement execute and checks if it worked or not
    die("Execute failed: " . $stmt->error);
}

echo "Lesson saved";//confirmation

//lines 1-67 by Benjamin Nguyen

