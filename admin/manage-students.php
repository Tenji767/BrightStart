<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../db_connect.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>BrightStart Admin Manage Student</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .page {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
        }

        h1 {
            margin: 0 0 6px 0;
            font-size: 42px;
            color: #1e293b;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
            font-size: 18px;
        }

        .home-btn {
            display: inline-block;
            text-decoration: none;
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s;
        }

        .home-btn:hover {
            background: #1d4ed8;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            margin-bottom: 28px;
        }

        .card h2 {
            margin-top: 0;
            margin-bottom: 22px;
            font-size: 28px;
        }

        .student-form {
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 16px;
            box-sizing: border-box;
            background: white;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .primary-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .primary-btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 14px 16px;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8fbff;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            text-decoration: none;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            font-size: 18px;
            transition: 0.2s;
        }

        .icon-btn:hover {
            background: #dbeafe;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            h1 {
                font-size: 34px;
            }

            .card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="header">
            <a href="admin-dashboard.php" class="home-btn">← Dashboard</a>
            <div>
                <h1>Student Management</h1>
                <p class="subtitle">Add and manage student accounts</p>
            </div>
        </div>

        <div class="card">
            <h2>Add New Student</h2>

            <form action="add-student.php" method="post" class="student-form">
                <div class="form-group">
                    <label for="student_name">Name</label>
                    <input type="text" id="student_name" name="student_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="grade_level">Grade Level</label>
                    <select id="grade_level" name="grade_level" required>
                        <option value="">Select grade level</option>
                        <option value="1">1st Grade</option>
                        <option value="2">2nd Grade</option>
                        <option value="3">3rd Grade</option>
                        <option value="4">4th Grade</option>
                        <option value="5">5th Grade</option>
                        <option value="6">6th Grade</option>
                        <option value="7">7th Grade</option>
                        <option value="8">8th Grade</option>
                        <option value="9">9th Grade</option>
                        <option value="10">10th Grade</option>
                        <option value="11">11th Grade</option>
                        <option value="12">12th Grade</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="school_id">School</label>
                    <select id="school_id" name="school_id" required>
                        <option value="">Select a school</option>
                        <?php
                        $stmt = $conn->prepare("SELECT school_id, school_name FROM School");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['school_id'] . "'>" . htmlspecialchars($row['school_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="primary-btn">Add Student</button>
            </form>
        </div>

<input type="text" id="search" placeholder="Search...">

<script>
document.getElementById("search").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
    });
});
</script>

<div id="filters">
<form action="manage-students.php" method="post">
<select id="filterschool" name="filterschool">
    <option value="" selected hidden>Select a school</option>
    <option value="">-</option>
    <?php
         $stmt = $conn->prepare("SELECT school_id, school_name FROM School");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['school_id'] . "'>" . htmlspecialchars($row['school_name']) . "</option>";
                            }
                        }
    ?>
</select>
<button type="submit">Filter</button>
</form>

</div>

<?php
    if($_POST['filterschool'] != ""){
        $stmt = $conn->prepare("SELECT student_id, student_name, StudentAccount.school_id, email, school_name, grade_id FROM StudentAccount JOIN School ON StudentAccount.school_id = School.school_id WHERE StudentAccount.school_id = ?");
        $stmt->bind_param("s", $_POST['filterschool']);

    }
    else {
        $stmt = $conn->prepare("SELECT student_id, student_name, StudentAccount.school_id, email, school_name, grade_id FROM StudentAccount JOIN School ON StudentAccount.school_id = School.school_id");

    }
?>

        <div class="card">
            <h2>Existing Students</h2>

            <div class="table-wrap">
                <table id="student-list">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Grade Level</th>
                        <th>School Affiliation</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['student_id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['grade_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['school_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td><a class='icon-btn' href='edit-student.php?student_id=" . $row['student_id'] . "'>&#9881;</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No students found</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<!-- File written by Benjamin N, styling by Jordan M -->