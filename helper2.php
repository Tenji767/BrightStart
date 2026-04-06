<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
}
if(isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header('location: login.php');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>BrightStart Home</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="helper.css">
</head>

<body>

<?php include('includes/nav.php');?>

    <div class="chat-container">
        <div id="chat-box"></div>
        <form id="chat-form">
            <input type="text" id="user-input" placeholder="Type a message..." required>
            <button type="submit">Send</button>
        </form>
        
    </div>

    <script src="script.js"></script><!--Script for the bot-->

    <footer>
        <p>&copy; 2025 BrightStart Math Tutoring. All rights reserved.</p>
    </footer>

</body>
</html>

<!-- Written by Nick DeBlock -->