<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== "admin"){
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>BrightStart Admin Control Panel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/admin/admin-dashboard.css">
</head>

<body>
    <div class="header">
        <a href="admin-dashboard.php" class="home-btn">Home</a>
        <h1>Admin Control Panel</h1>
        <a href="admin-account.php" class="home-btn">My Account</a>
    </div>

    <div class="button-group">
        <a href="manage-schools.php"><button>Manage Schools</button></a>
        <a href="manage-tutors.php"><button>Manage Tutors</button></a>
        <a href="manage-students.php"><button>Manage Students</button></a>
    </div>
<style>
body {
  margin: 0;
  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f4f7fb;
  align-items: center !important;
}

/* Header */
.header {
  display: flex;
  align-items: center;
  padding: 25px 40px;
}

h1 {
  font-size: 36px;
  color: #1e293b;
  text-align: left;
}

/* Home button */
.home-btn {
  background: #2563eb;
  color: white;
  border: none;
  top: 10px;
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.home-btn:hover {
  background: #1d4ed8;
}


/* Buttons */
.button-group {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 30px;
}

.button-group button {
  width: 180px;
  height: 120px;
  border: none;
  border-radius: 18px;
  font-size: 18px;
  font-weight: bold;
  color: white;
  background: #2563eb;
  cursor: pointer;
  box-shadow: 0 8px 18px rgba(0,0,0,0.1);
  transition: 0.3s;
}

.button-group button:hover {
  transform: translateY(-3px);
}
</style>
</body>




</html>

