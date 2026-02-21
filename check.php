<!-- Caleb McHaney this checks for the correct answer if the answer selected was incorrect the incorrect
  message will display -->
<!DOCTYPE html>
    <html>

<?php
// this file checks the answer and displays the result
session_start();
if (!isset($_SESSION["correct"])) {
    die("No active question.");
}
$grade_id = $_POST['grade_id'];
$concept_id = $_POST['concept_id'];


if (isset($_POST["answer"])) {
    $selected = $_POST["answer"];
    $correct = $_SESSION["correct"];
//if the selected answer is correct, display a message in green, otherwise display a message in red and show the correct answer
    if ($selected == $correct) {
        echo "<h2 style='color:green;'>Correct! </h2>";
    } else {
        echo "<h2 style='color:red;'>Incorrect </h2>";
        echo "<p>The correct answer was " . htmlspecialchars($correct) . "</p>";
    }
}

echo "<br><a href='quizes.php?grade_id=" 
     . $grade_id . "&concept_id=" 
     . $concept_id . "'>Next Question</a>";

?>
</html>