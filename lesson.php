<!DOCTYPE html>

<html>

<!--This entire head was copied from index.html. It contains the metadata and the header/navigation bar-->
    <head>
    <title>Learn</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Add stylesheet here -->
     <!--<link rel="stylesheet" href="lessons.css"> -->
</head>

<!-- <header>
    <a href="index.html"><img src="logo.png" alt="Brightstart logo"/></a>
    <h1>BrightStart</h1>
    <nav>
        <ul>
            <li><a href="lessons.php">Learn</a></li>
            <li><a href="practice.php">Practice</a></li>
            <li><a href="helper.html">Helper</a></li>
        </ul>
    </nav>

    <a href="account.html"><img src="pfp.png"/></a>
</header> -->

<!---->
<body>

<?php


$conn = new mysqli("sql112.infinityfree.com","DB_USER","DB_PASS","DB_NAME");

$grade = isset($_GET['grade_id']) ? urlencode($_GET['grade_id']) : '';//gets the grade id
$concept = urlencode($row['lesson_id']);//gets the concept id

$stmt = $conn->prepare("SELECT * FROM Lesson WHERE lesson_id=? AND grade_id=?");

$stmt->bind_param("ii", $concept, $grade);

$stmt->execute();

$result = $stmt->get_result();
$row=$result->fetch_assoc();



if(!$result){
    die("Query failed: ".$conn->error);
}

echo "<h1>" . $row['lesson_title'] . "</h1>";
echo $row['lesson_content_html'];


?>




</body>

</html>
<!-- Lines 1-56 written by Benjamin Nguyen -->