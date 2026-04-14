<!DOCTYPE html>
<html>
<head>
<title>Login</title>

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
    display: flex;  
    flex-direction: column;
    padding-top: 5px;
}
</style>

</head>

<body>

<div class="container">

<h2>Login</h2>

<form action="login_process.php" method="POST">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

<a href="register.php">Make an account</a>
<a href="password_reset.php">Forgot Password?</a>

</div>

</body>
</html>