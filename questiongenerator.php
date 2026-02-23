<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>

<!DOCTYPE html>
    <html>
<?php
session_start();
// Logs in to database (BN)
$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
else {
    echo "<p>Login successful</p>";
}



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

//gets the operator to be used in these questions and turns it into a safe string
$operation = $row['operations'];

$operation = trim(strtolower($operation));

if ($operation === "division") {//in case it's division, will try to prevent it from dividing by 0
    $y = rand(1, $max); // always prevent 0

    if ($row['require_whole_division']) {//if it needs to be divided into whole numbers for simplicitly, makes the number to be divided a multiple of the quotient
        $x = $y * rand(1, $max);
    }
}

//switch statements for arithmetic problems (will need to expand this to include different types of problems)
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
                // Newly added K–12 concepts start here
 
    // case "exponents": // exponents
    //     $answer = pow($x, 2);
    //     $question = "{$x}² = ?";
    //     $customQuestion = true;
    //     break;
 
    // case "square_root": // square roots
    //     $base = rand(2, 15);
    //     $perfect = $base * $base;
    //     $answer = $base;
    //     $question = "√{$perfect} = ?";
    //     $customQuestion = true;
    //     break;
 
    // case "percentage": // percentages
    //     $percent = rand(5, 100);
    //     $base = rand(20, 200);
    //     $answer = round(($percent / 100) * $base, 2);
    //     $question = "{$percent}% of {$base} = ?";
    //     $customQuestion = true;
    //     break;
 
    // case "area_triangle": // Geometry
    //     $base = rand(5, 20);
    //     $height = rand(5, 20);
    //     $answer = ($base * $height) / 2;
    //     $question = "Area of triangle (base={$base}, height={$height})?";
    //     $customQuestion = true;
    //     break;
 
    // case "area_circle": // Geometry
    //     $radius = rand(1, 15);
    //     $answer = round(pi() * $radius * $radius, 2);
    //     $question = "Area of circle (r={$radius})? (Use π)";
    //     $customQuestion = true;
    //     break;
 
    // case "slope": // Algebra 1
    //     $x1 = rand(1, 10);
    //     $y1 = rand(1, 10);
    //     $x2 = rand(11, 20);
    //     $y2 = rand(11, 20);
    //     $answer = round(($y2 - $y1) / ($x2 - $x1), 2);
    //     $question = "Find slope between ({$x1},{$y1}) and ({$x2},{$y2})";
    //     $customQuestion = true;
    //     break;
 
    // case "linear_equation": // Algebra 1
    //     $a = rand(1, 10);
    //     $b = rand(1, 20);
    //     $answer = rand(1, 10);
    //     $c = $a * $answer + $b;
    //     $question = "Solve: {$a}x + {$b} = {$c}";
    //     $customQuestion = true;
    //     break;
 
    // case "quadratic_eval": // Algebra 1
    //     $xVal = rand(1, 10);
    //     $answer = ($xVal * $xVal) + (3 * $xVal) + 2;
    //     $question = "Evaluate: x² + 3x + 2 when x = {$xVal}";
    //     $customQuestion = true;
    //     break;
 
    // default:
    //     die("Unsupported operation.");
    }

    //puts the question into a readable format
        $question = "$x $symbol $y = ?";
        
        //takes the correct answer and stores it
        $_SESSION["correct"] = $answer;

        // Generate answer options, starting with the correct answer
        $options = [$answer];

        while (count($options) < 4) {//generates fake answers within a margin of 5
            $range = max(5, intval($answer / 2));
            $wrong = $answer + rand(-$range, $range);
            if (!in_array($wrong, $options) && $wrong >= 0) {
                $options[] = $wrong;
            }
        }
            //shuffles it around
        shuffle($options);


?>


<h2><?php echo "$question"; ?></h2>
<!-- Displays the question -->
<form method="post" action="check.php"><!--This was written by Caleb, he can explain it better than i can-->
    <input type="hidden" name="grade_id" value="<?php echo $_GET['grade_id']; ?>">
    <input type="hidden" name="concept_id" value="<?php echo $_GET['concept_id']; ?>">
    <?php foreach ($options as $option): ?>
        <button type="submit" name="answer" value="<?php echo $option; ?>">
            <?php echo $option; ?>
        </button><br><br>
    <?php endforeach; ?>
</form>
</html>
<!--Lines 1-115 written by Benjamin Nguyen-->