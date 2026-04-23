<?php
include "db_connect.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Account</title>
<style>

 * {
    box-sizing: border-box;
}

body{
    font-family: Arial;
    background: linear-gradient(to right, #2563eb, #0891b2);
}

.container{
    width:400px;
    margin:auto;
    background:white;
    padding:25px;
    margin-top:100px;
    border-radius:8px;
}

input, select{
    width:100%;
    padding:10px;
    margin:8px 0;
}

button{
    width:100%;
    padding:10px;
    background:#007BFF;
    color:white;
    border:none;
}

a{
    font-size: small;
    padding-top:20px;
    color:#007BFF;
    text-decoration:none;
    padding-top: 5px;
}
</style>
</head>

<body>

<div class="container">

<h2>Create Account</h2>

<form action="create_account.php" method="POST">

<!-- <select name="account_type" required>
<option value="">Select Account Type</option>
<option value="student">Student</option>
<option value="teacher">Teacher</option>
</select> -->

<input type="text" name="name" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<input type="text" name="join_code" placeholder="School Join Code" required>

<select name="grade_id">
<option value="">Select Grade (Students Only)</option>
<option value="1">1st Grade</option>
<option value="2">2nd Grade</option>
<option value="3">3rd Grade</option>
<option value="4">4th Grade</option>
<option value="5">5th Grade</option>
<option value="6">6th Grade</option>
<option value="7">7th Grade</option>
<option value="8">8th Grade</option>
<option value="9">9th Grade</option>
<option value="10">10th Grade</option>
<option value="11">11th Grade</option>
<option value="12">12th Grade</option>
</select>

<button type="submit">Create Account</button>

</form>
<a href="login.php">Already have an account? Login</a>
</div>

</body>
</html>