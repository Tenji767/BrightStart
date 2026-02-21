<!DOCTYPE html>
    <html>
<?php
session_start();
// Logs in to database (BN)
$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P!", "if0_41201125_brightstar_db");

// Gets the grade id and concept id using GET, which takes from url (BN)
$grade_id = isset($_GET['grade_id']) ? (int)$_GET['grade_id'] : 0;
$concept_id = isset($_GET['concept_id']) ? (int)$_GET['concept_id'] : 0;

// sql query to get a random question template from a concept within that grade (will add school partitioning later) (BN)
$stmt = $conn->prepare("SELECT * FROM QuestionTemplate WHERE gradeID = ? AND conceptID = ? ORDER BY RAND() LIMIT 1");
$stmt->bind_param("ii", $grade_id, $concept_id);
$stmt->execute();

// gets the result and stores it into a variable, then creates an associative array to call each value of the attribute of the called question template
$result=$stmt->get_result();

$row = $result->fetch_assoc();

// Generate question (if this gets too big, add to another file)
// Will add multiple types of problems, will separate into different files. this will be arithmetic


// Checks to see if there is a question template loaded.
if (!$row) {
    die("No question template found.");
}

// Set values for the smallest or largest number used in the problem (for complexity)
$min = $row['min_value'];
$max = $row['max_value'];

//generates the values to be used in the problem, uses previously defined min max variables
$x = rand($min, $max);
$y = rand($min, $max);


$operation = $row['operations'];


if ($operation === "division") {
    $y = rand(1, $max); // always prevent 0

    if ($row['require_whole_division']) {
        $x = $y * rand(1, $max);
    }
}


    switch ($operation) {
        case "addition":
            $answer = $x + $y;
            $symbol = "+";
            break;

        case "subtraction":
            if (!$row['allow_negative'] && $x < $y) {
                [$x, $y] = [$y, $x];
            }
            $answer = $x - $y;
            $symbol = "-";
            break;

        case "multiplication":
            $answer = $x * $y;
            $symbol = "×";
            break;

        case "division":
            $answer = $x / $y;
            $symbol = "÷";
            break;
    }

    
        $question = "$x $symbol $y = ?";
        
                
        $_SESSION["correct"] = $answer;

        // Generate answer options
        $options = [$answer];

        while (count($options) < 4) {
            $range = max(5, intval($answer / 2));
            $wrong = $answer + rand(-$range, $range);
            if (!in_array($wrong, $options) && $wrong >= 0) {
                $options[] = $wrong;
            }
        }

        shuffle($options);


?>


<h2><?php echo "$question"; ?></h2>

<form method="post" action="check.php">
    <?php foreach ($options as $option): ?>
        <button type="submit" name="answer" value="<?php echo $option; ?>">
            <?php echo $option; ?>
        </button><br><br>
    <?php endforeach; ?>
</form>
</html>
