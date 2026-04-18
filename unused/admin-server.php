<?php
/**
 * I COPIED THIS ENTIRE THING GROM WESTFALL'S REGISTRATION EXAMPLE
 * I DON'T KNOW HOW THIS WORKS
 */
session_start();

//Initializing Variables
$username = "";
$email = "";
$errors = array();

//Connect to the database
$db = mysqli_connect('localhost', 'jwestfal_student', 'student#2025', 'jwestfal_registration'); //CHANGE THIS

//Register User
if(isset($_POST['reg_user'])) {
    //Receive all input values from the form
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

//Form Validtion
//Add (array_push) errors into $errors array
if(empty($username)) {array_push($errors, "Username is Required");}
if(empty($email)) {array_push($errors, "Email is Required");}
if(empty($password_1)) {array_push($errors, "Password is Required");}
if($password_1 != $password_2) {array_push($errors, "The two passwords do not match");
}

//First check the database that a username and email does not exist already
$user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
$result = mysqli_query($db, $user_check_query);
$user = mysqli_fetch_assoc($result);


if ($user) { //If user exists (Fixed)
if($user['username'] === $username) {
    array_push($errors, "Username already exists");
    }

if($user['email'] === $email) {
    array_push($errors, "Email aleady exists");
    }
}

//Now register the user if no errors in the form
if(count($errors) == 0) {
    $password = md5($password_1); //Encrypts the password before inserting into database

    $query = "INSERT INTO users(username, email, password) VALUES('$username', '$email', '$password')";
    mysqli_query($db, $query);

    $_SESSION['username']=$username;
    $_SESSION['success']="You are now logged in";
    header('location: admin-dashboard.php');
}
} // added

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

<!-- By Benjamin Nguyen -->