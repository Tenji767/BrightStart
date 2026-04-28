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

$uploadDir = __DIR__ . "/uploads/";

if (!empty($_FILES)) {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    if (!is_writable($uploadDir)) {
        die("Uploads directory is not writable.");
    }
    foreach ($_FILES as $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo "Upload error for " . $file['name'] . ": " . $file['error'] . "<br>";
            continue;
        }
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Invalid file type for " . $file['name'] . "<br>";
            continue;
        }
        $path = $uploadDir . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            echo "Failed to move: " . $file['name'] . "<br>";
        }
    }
}

$lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : null;

if ($lesson_id) {
    $stmt = $conn->prepare("UPDATE Lesson SET lesson_title = ?, grade_id = ?, lesson_content_html = ? WHERE lesson_id = ?");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("sisi", $title, $grade, $html, $lesson_id);
} else {
    $stmt = $conn->prepare("INSERT INTO Lesson (lesson_title, grade_id, lesson_content_html, school_id, teacher_id) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("sisii", $title, $grade, $html, $school_id, $_SESSION['user_id']);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

echo $lesson_id ? "Lesson updated successfully" : "Lesson saved successfully";

//lines 1-84 by Benjamin Nguyen

