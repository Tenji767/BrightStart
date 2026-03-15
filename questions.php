<!-- Author: Caleb McHaney -->
<!-- Questions.php is the main file for handling questions -->


<!DOCTYPE html>
<html lang="en">
<head>

<!-- reports errors and displays them -->
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); // development error checking
?>

 <?php
//Database connection
$conn = new mysqli("sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>