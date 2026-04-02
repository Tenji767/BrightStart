<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);//development error checking
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="concepts.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>

<?php include('includes/nav.php');?>
<body>
<?php

// echo "<p>Loaded</p>";
$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}
// else {
//     echo "<p>Login successful</p>";
// }

if (!isset($_GET['grade_id'])) {//if accessed outside of from practice page, ereturns error
    die("Grade not specified.");
}

$grade_id = $_GET['grade_id'];//gets the grade that was selected into a variable

$stmt = $conn->prepare("SELECT * FROM Lesson WHERE grade_id = ?");//loads that grade_id variable into the query to get all related concepts
$stmt->bind_param("i", $grade_id);
$stmt->execute();

$result = $stmt->get_result();//puts results intovariable

if(!$result){
    echo "<p>Error loading in concepts</p>";//welp...hopefully you have concepts in the database
}

echo "<a href='choose-grade.php'>";
echo "<div><p>Select Grade</p></div>";
echo "</a>";


while ($row = $result->fetch_assoc()) {//puts the results into an array that can be referenced by the name of the column
    $grade = isset($_GET['grade_id']) ? urlencode($_GET['grade_id']) : '';//gets the grade id
    $concept = urlencode($row['lesson_id']);//gets the concept id

    echo "<div>";
    // echo "<a href='learnconcept.php?grade_id=$grade&concept_id=$concept'><p>Learn " . htmlspecialchars($row['conceptDesc']) . "</p></a>";
    // echo "<a href='quizes.php?grade_id=$grade&concept_id=$concept'><p>" . htmlspecialchars($row['conceptDesc']) . "</p></a>";//will open the quiz section that will pull questions templates from the database REDO THE QUIZES TO PULL FROM DATABASE INSTEAD OF GENERATE
    echo '<button class="concept-btn" data-concept="' . $row['lesson_id'] . '">' . $row['lesson_title'] . '</button>';
    echo "</div>";

}
?>

<div id="concept-action-menu">
    <p id="selectedConceptText"></p>
    <div id="concept-action-btns">
        <button id="learnBtn">Learn</button>
        <button id="practiceBtn">Practice</button>
    </div>
</div>


<script>
    let selectedConcept = null;
const learnBtn = document.getElementById("learnBtn");
const practiceBtn = document.getElementById("practiceBtn");

    document.querySelectorAll('.concept-btn').forEach(button => {
        button.addEventListener("click", ()=> {
            selectedConcept = button.dataset.concept;

            document.getElementById("concept-action-menu").style.display="block";
            document.getElementById("selectedConceptText").textContent = "Selected: " + button.textContent;
        });
    });

    const params = new URLSearchParams(window.location.search);
    const gradeID = params.get("grade_id");

    learnBtn.addEventListener("click", () => {

  if(selectedConcept){
    window.location.href =
      `lesson.php?grade_id=${gradeID}&concept_id=${selectedConcept}`;
  }

});

practiceBtn.addEventListener("click", () => {

  if(selectedConcept){
    window.location.href =
      `practice.php?lesson_id=${selectedConcept}`;
  }

});
</script>
</body>

</html>
<!-- lines 1-112 written by Benjamin Nguyen -->