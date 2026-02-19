<!-- Caleb McHaney This program generates the selected depending on the operation selected then 
 generates the values for the variables making the correct answer and the three incorrect answers. -->
<!DOCTYPE html>
    <html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$data = json_decode(file_get_contents("questiontemplate.json"), true);

$template = $data[0];

$min = $template["limits"]["min"];
$max = $template["limits"]["max"];

// Pick random operation
$operation = $template["operations"][array_rand($template["operations"])];
$_SESSION["operation"] = $operation;

// Generate numbers
$x = rand($min, $max);
$y = rand($min, $max);

// Prevent divide by zero
if ($operation === "division") {
    $y = rand(1, $max); // never 0
    $x = $y * rand(0, $max); // ensures whole number answer
}

switch ($operation) {

    case "addition":
        $correctAnswer = $x + $y;
        $symbol = "+";
        break;

    case "subtraction":
        // Optional: prevent negative answers
        if ($x < $y) {
            $temp = $x;
            $x = $y;
            $y = $temp;
        }
        $correctAnswer = $x - $y;
        $symbol = "-";
        break;

    case "multiplication":
        $correctAnswer = $x * $y;
        $symbol = "×";
        break;

    case "division":
        $correctAnswer = $x / $y;
        $symbol = "÷";
        break;
}

$_SESSION["correct"] = $correctAnswer;

// Generate answer options
$options = [$correctAnswer];

while (count($options) < 4) {
    $wrong = $correctAnswer + rand(1, 10);
    if (!in_array($wrong, $options) && $wrong >= 0) {
        $options[] = $wrong;
    }
}

shuffle($options);
?>


<h2><?php echo "$x $symbol $y = ?"; ?></h2>

<form method="post" action="check.php">
    <?php foreach ($options as $option): ?>
        <button type="submit" name="answer" value="<?php echo $option; ?>">
            <?php echo $option; ?>
        </button><br><br>
    <?php endforeach; ?>
</form>

</html>
