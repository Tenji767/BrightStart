<?php
include("db_connect.php");

$school_id = 0;
$admin_name = "AgileNinjas2026-4";
$email = "agileninjascapstone@gmail.com";

$password_hash = password_hash("miss551ngn033", PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO AdminAccount (school_id, admin_name, email, password_hash) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $school_id, $admin_name, $email, $password_hash);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Admin account created successfully.";
} else {
    echo "Error creating admin account: " . $stmt->error;
}
?>