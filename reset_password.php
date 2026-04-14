<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

require __DIR__ . "/db_connect.php";


$sql = "SELECT * FROM StudentAccount
        WHERE reset_token_hash = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
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
<body>
<div class="container">

    <h2>Reset Password</h2>

    <form method="post" action="process_reset_password.php">

        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New password</label>
        <input type="password" id="password" name="password">

        <label for="password_confirmation">Repeat password</label>
        <input type="password" id="password_confirmation"
               name="password_confirmation">

        <button>Send</button>
    </form>
</div>
</body>
</html>