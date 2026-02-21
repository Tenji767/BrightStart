<!-- Caleb McHaney this checks for the correct answer if the answer selected was incorrect the incorrect
  message will display -->
<!DOCTYPE html>
    <html>

<?php
// this file checks the answer and displays the result
session_start();
//Benjamin Nguyen modified lines 11, 17, 18, and 29-31, added lines 13-14
if (!isset($_SESSION["correct"])) { //If the session starts and there is no correct answer loaded from the question generator, will die and alert the browser
    die("No active question.");
}
$grade_id = $_POST['grade_id'];//gives these post variables a variable to be used in
$concept_id = $_POST['concept_id'];


if (isset($_POST["answer"])) {//once the user selects an answer, it will send the answer via POST and run this segment
    $selected = $_POST["answer"];//sets the selected answer to the one selected
    $correct = $_SESSION["correct"];//sets the correct answer to the correct one carried over from questiongenerator
//if the selected answer is correct, display a message in green, otherwise display a message in red and show the correct answer
    if ($selected == $correct) {
        echo "<h2 style='color:green;'>Correct! </h2>";
    } else {
        echo "<h2 style='color:red;'>Incorrect </h2>";
        echo "<p>The correct answer was " . htmlspecialchars($correct) . "</p>";
    }
}
//will display a button that will lead the user back to another question
echo "<br><a href='quizes.php?grade_id=" 
     . $grade_id . "&concept_id=" 
     . $concept_id . "'>Next Question</a>";

?>
</html>