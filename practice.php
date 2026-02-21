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
            <li><a href="practice.php">Practice</a></li>
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
        <p>List of grades</p>
 <?php

$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");
// log in and check to see if the query worked
$result = $conn->query("SELECT * FROM Grades");
if (!$result) {
    die("Query failed: " . $conn->error);
}
// loops through the list of grades and the database and displays a link to the concepts related to that grade
while ($row = $result->fetch_assoc()) {
    echo "<a href='concepts.php?grade_id={$row['gradeID']}'>";
    echo "<div><p>{$row['gradeDesc']}</p></div>";
    echo "</a>";
}

?>


    </div>
</body>

</html>
<!-- Lines 1-56 written by Benjamin Nguyen -->