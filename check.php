<!-- Caleb McHaney this checks for the correct answer if the answer selected was incorrect the incorrect message will display -->
<doctype html>
    <html>

<?php
// this file checks the answer and displays the result
session_start();

if (isset($_POST["answer"])) {
    $selected = $_POST["answer"];
    $correct = $_SESSION["correct"];
//if the selected answer is correct, display a message in green, otherwise display a message in red and show the correct answer
    if ($selected == $correct) {
        echo "<h2 style='color:green;'>Correct! </h2>";
    } else {
        echo "<h2 style='color:red;'>Incorrect </h2>";
        echo "<p>The correct answer was $correct</p>";
    }
}

echo "<br><a href='index.php'>Next Question</a>";
?>
</html>