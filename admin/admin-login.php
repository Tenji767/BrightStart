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

<?php

session_start();
$username = "";
$email = "";
$errors = array();


//Login User
if(isset($_POST['login_user'])) {
    $username=mysqli_real_escape_string($db, $_POST['username']);
    $password=mysqli_real_escape_string($db, $_POST['password']);

    if(empty($username)) {
        array_push($errors, "Username is Required");
    }
    if(empty($password)) {
        array_push($errors, "Password is Required");
    }

    if(count($errors) == 0) {
        $password=md5($password);
        $query="SELECT * FROM users WHERE username='$username' AND password='$password'";
        $results=mysqli_query($db, $query);
        if(mysqli_num_rows($results) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            header('location: index.php');
        }else {
            array_push($errors, "Wrong Username/Password Combination");
        }
    }
}


?>


</html>

<!-- By Benjamin Nguyen -->