<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_POST['lesson_id']) && isset($_POST['is_enabled'])) {
    $id = intval($_POST['lesson_id']);
    // Flip the value: if it was 1 set to 0, if it was 0 set to 1
    $newStatus = intval($_POST['is_enabled']) === 1 ? 0 : 1;

    $stmt = $conn->prepare("UPDATE Lesson SET is_enabled = ? WHERE lesson_id = ?");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $newStatus, $id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
}

header("Location: admin-manage-lessons.php");
// this file handles the toggling of lessons
// Author: Caleb McHaney
// lines 1-31 written by Caleb McHaney
exit;
