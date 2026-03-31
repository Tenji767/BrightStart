<!DOCTYPE html>

<html>

<!--This entire head was copied from index.html. It contains the metadata and the header/navigation bar-->
    <head>
    <title>Learn</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Add stylesheet here -->
    <link rel="stylesheet" href="choose-grade.css">
    <link rel="stylesheet" href="style.css">
</head>

<?php include('includes/nav.php');?>

<!---->
<body>

    <div class="grades-menu">
        <!--loop through grade levels
        Add functionality to put the last practiced or viewed grade on top-->
        <p>List of grades</p>
 <?php



$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    echo "Database Connection failed";
    die("Database connection failed: " . $conn->connect_error);
    //check forc connection
    
  
}

// log in and check to see if the query worked
$result = $conn->query("SELECT * FROM Grade");
if (!$result) {
    die("Query failed: " . $conn->error);
}
// loops through the list of grades and the database and displays a link to the concepts related to that grade
while ($row = $result->fetch_assoc()) {
    echo "<a href='concepts.php?grade_id={$row['grade_id']}'>";
    echo "<div><p>{$row['grade_name']}</p></div>";
    echo "</a>";
}

?>


    </div>
</body>

</html>
<!-- Lines 1-56 written by Benjamin Nguyen -->