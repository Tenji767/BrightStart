<?php
// session_start();

// if(!isset($_SESSION($username))) {
//     $_SESSION['msg'] = "Log in first";
//     //Incorporate the login session
//     //         $_SESSION['msg'] = "You must log in first";
//     //     header('location: login.php');
//     // }
//     // if(isset($_GET['logout'])) {
//     //     session_destroy();
//     //     unset($_SESSION['username']);
//     //     header('location: login.php');
//     // }

// }


?>




<!DOCTYPE html>
<html>

<!--Hello! This is the beginning of the BrightStart (name in progress) skeleton. Hopefully this goes well.-->


<!-- Things to add:
 - Incorporate routing
 - encrypt link data 
 - turn this into php to allow for database access
 - add REACT functionality-->


<head>
    <title>BrightStart Home</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Add stylesheet here -->
</head>

<header>
    <a href="index.html"><img src="logo.png" alt="Brightstart logo"/></a>
    <h1>BrightStart</h1>
    <nav>
        <ul>
            <li><a href="lessons.php">Learn</a></li>
            <li><a href="practice.php">Practice</a></li>
            <li><a href="helper.html">Helper</a></li>
 
        </ul>
    </nav>

</header>

<body>

    <!--Add a "continue practice" option to pick up from where session was left off from last-->
    <div class="menu">

        <div class="menu-item" id="lesson-menu">
            <a href="lessons.html"><button>Learn</button></a>
        </div>

        <div class="menu-item" id="practice-menu">
            <a href="practice.php"><button>Practice</button></a>
        </div>

        <div class="menu-item" id="lesson-menu">
            <a href="helper.html"><button>Helper</button></a>
        </div>

    </div>
</body>

<footer>
    <!--insert footer comment here-->
</footer>

</html>

<!-- Lines 1-61 written by Benjamin Nguyen -->