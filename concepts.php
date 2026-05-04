<?php
session_start();
$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'student' && $role !== 'teacher' && $role !== 'admin')) {
    header("Location: login.php");
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);//development error checking
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="concepts.css">

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
$school_id = intval($_SESSION['school_id'] ?? 0);

// checks the database if the lesson has been viewed by the student before if so it will show an indicator for that lesson 
$viewed = [];
if ($role === 'student') {
    $student_id = intval($_SESSION['user_id']);
    $vStmt = $conn->prepare("SELECT lesson_id FROM LessonHistory WHERE student_id = ?");
    $vStmt->bind_param("i", $student_id);
    $vStmt->execute();
    $vResult = $vStmt->get_result();
    while ($vRow = $vResult->fetch_assoc()) {
        $viewed[$vRow['lesson_id']] = true;
    }
    $vStmt->close();
}

// checks for concepts assigned to that grade and school, and whether they have content and questions
$stmt = $conn->prepare(
    "SELECT l.lesson_id, l.lesson_title,
            (l.lesson_content_html IS NOT NULL AND l.lesson_content_html != '') AS has_lesson,
            (SELECT COUNT(*) FROM Questions q WHERE q.lesson_id = l.lesson_id) > 0 AS has_questions
     FROM Lesson l
     WHERE l.grade_id = ? AND l.school_id = ?"
);//loads grade_id and school_id to only return concepts belonging to this school
$stmt->bind_param("ii", $grade_id, $school_id);
$stmt->execute();

$result = $stmt->get_result();//puts results intovariable

if(!$result){
    echo "<p>Error loading in concepts</p>";//welp...hopefully you have concepts in the database
}

echo "<a href='choose-grade.php'>";
echo "<div><p>Select Grade</p></div>";
echo "</a>";


while ($row = $result->fetch_assoc()) {//puts the results into an array that can be referenced by the name of the column
    $hasLesson    = $row['has_lesson']    ? 'true' : 'false';
    $hasQuestions = $row['has_questions'] ? 'true' : 'false';
    $hasViewed    = isset($viewed[$row['lesson_id']]) ? 'true' : 'false';

    echo "<div>";
    echo '<button class="concept-btn" data-concept="' . $row['lesson_id'] . '" data-has-lesson="' . $hasLesson . '" data-has-questions="' . $hasQuestions . '" data-viewed="' . $hasViewed . '">' . htmlspecialchars($row['lesson_title']) . '</button>';
    echo "</div>";

}
?>

<div id="concept-action-menu">
    <p id="selectedConceptText"></p>
    <div id="concept-action-btns">
        <button id="learnBtn">Learn<span id="learnViewedBadge" class="viewed-badge" style="display:none;">&#10003;</span></button>
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

            const hasLesson    = button.dataset.hasLesson    === 'true';
            const hasQuestions = button.dataset.hasQuestions === 'true';
            const hasViewed    = button.dataset.viewed       === 'true';

            learnBtn.disabled    = !hasLesson;
            practiceBtn.disabled = !hasQuestions;
            document.getElementById('learnViewedBadge').style.display = hasViewed ? 'inline-block' : 'none';
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
<!-- lines 1, 8-17, 19, 21-43, 45, 69-82, 86-87, 89-96, 98-113, 122-140, 142-149 written by Benjamin Nguyen -->
<!-- lines 2-7, 44, 46-68, 83-85, 88, 97, 114-121, 141 written by Caleb McHaney -->
<!-- lines 18, 20 written by Jordan Munster -->