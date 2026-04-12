<?php
session_start();
include "db_connect.php";

// Generate a CSRF token for this session if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Account</title>
<style>
body{
    font-family: Arial;
    background:#f4f4f4;
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
</style>
</head>

<body>

<div class="container">

<h2>Create Account</h2>

<form action="create_account.php" method="POST">

<!-- CSRF token: hidden field validated server-side -->
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

<select name="account_type" required>
<option value="">Select Account Type</option>
<option value="student">Student</option>
<option value="teacher">Teacher</option>
</select>

<input type="text" name="name" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password (min 8 characters)" required minlength="8">

<input type="text" name="join_code" placeholder="School Join Code" required>

<select name="grade_id">
<option value="">Select Grade (Students Only)</option>
<option value="1">Grade 9</option>
<option value="2">Grade 10</option>
</select>

<button type="submit">Create Account</button>

</form>

</div>

</body>
</html>
