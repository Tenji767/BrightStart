<!DOCTYPE html>
<html>
<head>
<title>Password Reset</title>

<style>
 * {
    box-sizing: border-box;
}

body{
    font-family:Arial;
    background: linear-gradient(to right, #2563eb, #0891b2);
}

.container{
    width:350px;
    margin:auto;
    margin-top:120px;
    background:white;
    padding:25px;
    border-radius:8px;
}

input{
    width:100%;
    padding:10px;
    margin:10px 0;
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
    margin-top:25;
    color:#007BFF;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="container">

<h2>Password Reset</h2>

<form action="send_password_reset.php" method="POST">

<input type="email" name="email" id="Email" placeholder="Email" required>

<button type="submit">Send Reset Link</button>

</form>

<a href="login.php">Return to Login</a>

</div>

</body>
</html>