<?php

$host = "sql311.infinityfree.com";
$user = "if0_40511546";
$password = "agile4fragile";
$db = "if0_40511546_testdb";

$conn = new mysqli($host,$user,$password,$db);

if($conn->connect_error){
    die("Connection failed");
}

?>