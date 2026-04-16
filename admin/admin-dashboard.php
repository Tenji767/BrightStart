<?php
session_start();
include("../db_connect.php");

?>

<!DOCTYPE html>
<html>
<head>
    <title>BrightStart Admin Control Panel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-dashboard.css">
</head>

<body>
<div class="container">
  <p>Select a section to manage</p>

    <div class="header">
        <a href="../admin-dashboard.php"><button>Home</button></a>
        <h1>Admin Control Panel</h1>
    </div>

    <div class="nav">
        <a href="manage-schools.php"><button>Manage Schools</button></a>
        <a href="manage-tutors.php"><button>Manage Tutors</button></a>
        <a href="manage-students.php"><button>Manage Students</button></a>
    </div>
</div>
</body>




</html>

