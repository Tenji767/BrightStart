<?php 
?>

<!DOCTYPE html>
<html>
// This page allows the admin to create a new student account by filling out a form with the student's details. The form includes fields for the student's name, email, username, grade, status, and date added. Once the form is submitted, the data can be processed to create the new student account in the system.
<head>
    <meta charset="UTF-8">
    <title>Create Student Account Page</title>
    </head>

    <h1>Create Student Account Page</h1>

        <br>
        <div class ="student-account-list">
            <a href="student-account-manage.php"><button>View Student Accounts</button></a>
            <a href="admin-dashboard.php"><button>Back to Dashboard</button></a>
            </div>

    <body>
// The form for creating a new student account
        <form action="student-account-create.php" method="post">
            <label for="name">Student Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>

            <label for="grade">Grade:</label><br>
            <input type="number" id="grade" name="grade" min="1" max="12" required><br><br>

            <label for="status">Status:</label><br>
            <select id="status" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select><br><br>

            <label for="date_added">Date Added:</label><br>
            <input type="date" id="date_added" name="date_added" required><br><br>

            <label for="date_added">Actions:</label><br>
            <input type="date" id="date_added" name="date_added" required><br><br>



            <input type="submit" value="Create Account">
        </form>

    </body>
</html>

<!-- by Noah Reynolds-->



        

