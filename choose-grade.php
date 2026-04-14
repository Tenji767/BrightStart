<!DOCTYPE html>

<html>

<!--This entire head was copied from index.html. It contains the metadata and the header/navigation bar-->
    <head>
    <title>Learn</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Add stylesheet here -->
     <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="choose-grade.css">
    
</head>

<?php include('includes/nav.php');?><!--Includes the navigation bar for simplicity-->

<!---->
<body>

    <div class="grades-menu">
        <!--loop through grade levels
        Add functionality to put the last practiced or viewed grade on top-->
        <p>List of grades</p>
        <div class="grade-list">
 <?php



$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    echo "Database Connection failed";
    die("Database connection failed: " . $conn->connect_error);
    //check for connection
    
  
}

// log in and check to see if the query worked
$result = $conn->query("SELECT grade_id, grade_name FROM Grade WHERE grade_id BETWEEN 1 AND 12 ORDER BY grade_id ASC");
if (!$result) {
    die("Query failed: " . $conn->error);
}
// loops through the list of grades and the database and displays a link to the concepts related to that grade
while ($row = $result->fetch_assoc()) {
    $gradeName = trim((string)$row['grade_name']);
    $gradeNameLower = strtolower($gradeName);

    if ($gradeName === '') {
        continue;
    }

    if (
        strcasecmp($gradeName, 'List of grades') === 0 ||
        strpos($gradeNameLower, 'hidden') !== false ||
        strpos($gradeNameLower, 'button') !== false
    ) {
        continue;
    }

    echo "<a href='concepts.php?grade_id={$row['grade_id']}'>";
    echo "<div><p>" . htmlspecialchars($gradeName, ENT_QUOTES, 'UTF-8') . "</p></div>";
    echo "</a>";
}

?>


        </div>


    </div>
</body>

</html>
<!-- Lines 1-56 written by Benjamin Nguyen -->