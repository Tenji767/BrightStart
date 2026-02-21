<?php


?>

<!DOCTYPE html>
<html>
<!-- This line is supposed to have a trailing menu so that users know what they clicked previously, but it's not turning out how i like it. -->
<p><?php echo $_GET['grade_id'] . ">" . $_GET["concept_id"] ?> </p>


<?php include('questiongenerator.php')?><!--This entire page is just a placeholder for questiongenerator-->
</html>

<!-- written by Benjamin Nguyen -->
 <!-- Yes, there is a typo but I have gone too far -->