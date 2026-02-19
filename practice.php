<!DOCTYPE html>

<html>

<!--This entire head was copied from index.html. It contains the metadata and the header/navigation bar-->
    <head>
    <title>Learn</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Add stylesheet here -->
</head>

<header>
    <a href="index.html"><img src="logo.png" alt="Brightstart logo"/></a>
    <h1>BrightStart</h1>
    <nav>
        <ul>
            <li><a href="lessons.html">Learn</a></li>
            <li><a href="practice.html">Practice</a></li>
            <li><a href="helper.html">Helper</a></li>
        </ul>
    </nav>

    <a href="account.html"><img src="pfp.png"/></a>
</header>

<!---->
<body>

    <div class="grades-menu">
        <!--loop through grade levels
        Add functionality to put the last practiced or viewed grade on top-->
<?php include('grades.php');?>

    </div>
</body>

</html>