<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
body{
    font-family:Arial;
    background:#f4f4f4;
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

</div>

</body>
</html>
<!-- lines 1-57 written by Nicholas Deblock -->