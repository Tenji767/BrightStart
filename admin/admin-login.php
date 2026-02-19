<?php
include("admin-server.php");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
</head>


<h1>This is for admins to BrightStar only</h1>


<form method="post" action="login.php">

    <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" />
    </div>
    <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" />
    </div>
    <div class="input-group">
        <button type="submit" class="btn" name="login_user">Login</button>
    </div>




</html>

<!-- By Benjamin Nguyen -->