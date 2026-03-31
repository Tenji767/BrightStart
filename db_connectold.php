<?php


$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

    //new database is localhost, brights1_adminuser, agileninjascapstone2025, brights1_dbprimary
}
?>

