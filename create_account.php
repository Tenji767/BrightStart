<?php
session_start();
include "db_connect.php";

// ── CSRF verification ────────────────────────────────────────────────────────
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    die("Invalid request.");
}

// ── Collect and sanitise inputs ──────────────────────────────────────────────
$type      = $_POST['account_type'] ?? '';
$name      = trim($_POST['name']      ?? '');
$email     = trim($_POST['email']     ?? '');
$raw_pass  = $_POST['password']       ?? '';
$join_code = trim($_POST['join_code'] ?? '');
$grade_id  = isset($_POST['grade_id']) ? (int) $_POST['grade_id'] : 0;

// Basic validation
if (!in_array($type, ['student', 'teacher'], true)) {
    die("Invalid account type.");
}
if ($name === '' || $email === '' || $raw_pass === '' || $join_code === '') {
    die("All fields are required.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}
if (strlen($raw_pass) < 8) {
    die("Password must be at least 8 characters.");
}

$password = password_hash($raw_pass, PASSWORD_DEFAULT);

// ── Look up school with prepared statement ───────────────────────────────────
$stmt = $conn->prepare("
    SELECT * FROM School
    WHERE student_join_code = ?
       OR teacher_join_code = ?
");
$stmt->bind_param("ss", $join_code, $join_code);
$stmt->execute();
$schoolResult = $stmt->get_result();

if ($schoolResult->num_rows === 0) {
    die("Invalid join code.");
}

$school    = $schoolResult->fetch_assoc();
$school_id = $school['school_id'];

// ── Create Student ───────────────────────────────────────────────────────────
if ($type === 'student') {

    if ($join_code !== $school['student_join_code']) {
        die("Invalid student join code.");
    }
    if ($grade_id <= 0) {
        die("Please select a grade.");
    }

    // Check for duplicate email
    $check = $conn->prepare("SELECT student_id FROM StudentAccount WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("An account with that email already exists.");
    }

    $stmt = $conn->prepare("
        INSERT INTO StudentAccount (school_id, grade_id, student_name, email, password_hash)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisss", $school_id, $grade_id, $name, $email, $password);
}

// ── Create Teacher ───────────────────────────────────────────────────────────
if ($type === 'teacher') {

    if ($join_code !== $school['teacher_join_code']) {
        die("Invalid teacher join code.");
    }

    // Check for duplicate email
    $check = $conn->prepare("SELECT teacher_id FROM TeacherAccount WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("An account with that email already exists.");
    }

    $stmt = $conn->prepare("
        INSERT INTO TeacherAccount (school_id, teacher_name, email, password_hash)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $school_id, $name, $email, $password);
}

// ── Execute and redirect ─────────────────────────────────────────────────────
if ($stmt->execute()) {
    // Clear CSRF token after successful use
    unset($_SESSION['csrf_token']);
    header("Location: login.php");
    exit();
} else {
    http_response_code(500);
    error_log("Account creation error: " . $stmt->error); // log privately, don't expose to user
    die("Something went wrong. Please try again.");
}
?>
