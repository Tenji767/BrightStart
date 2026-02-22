<?php

$created = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $created = true;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Tutor</title>
</head>
<body>
    <H1>
        Create Tutor </H1>
        <div>
    <!-- Buttons for going to see all the tutors and a simple back to main dashboard button. -->
    <a href="tutor-manage.php"><button>View Tutors</button></a>
    <a href="admin-dashboard.php"><button>Back to Dashboard</button></a>

    </div>
<!-- Used the same information that was needed for the tutor like their name, email, username, and password and a pop up message letting the admin know that the tutor was created. When the database is connected with this it will allow the admin to also confirm the tutor was created by simply just looking at the tutor-manage page and seeing the List of tutors on there. -->
<br>
<?php if ($created): ?>
    <p><strong>Tutor created. It will show on the tutor-manage.php page after database is connected.</strong></p>
<?php endif; ?>

<form action="admin-tutor-create.php" method="post">
    <label for="name">Tutor Name:</label><br>
    <input type="text" id="name" name="name" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="subject">Username:</label><br>
    <input type="text" id="subject" name="subject" required><br><br>

    <label for="subject">Password:</label><br>
    <input type="text" id="subject" name="subject" required><br><br>

    <input type="submit" value="Create Tutor">
</form>
<br>
</body></html>

<!-- by Noah Reynolds-->
