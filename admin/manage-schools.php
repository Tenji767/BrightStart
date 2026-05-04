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
    <title>BrightStart Admin Manage Schools</title>
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

        .school-form {
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

        input[type="text"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="text"]:focus {
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
                <h1>Schools</h1>
                <p class="subtitle">Manage participating schools</p>
            </div>
        </div>

        <div class="card">
            <h2>Add New School</h2>

            <form action="add-school.php" method="post" class="school-form">
                <div class="form-group">
                    <label for="school_name">School Name</label>
                    <input type="text" id="school_name" name="school_name" required>
                </div>

                <div class="form-group">
                    <label for="student_join_code">Student Join Code</label>
                    <input type="text" id="student_join_code" name="student_join_code" required>
                </div>

                <div class="form-group">
                    <label for="teacher_join_code">Teacher Join Code</label>
                    <input type="text" id="teacher_join_code" name="teacher_join_code" required>
                </div>

                <button type="submit" class="primary-btn">Add School</button>
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
        <div class="card">
            <h2>Existing Schools</h2>

            <div class="table-wrap">
                <table id="school-list">
                    <tr>
                        <th>School ID</th>
                        <th>School Name</th>
                        <th>Student Join Code</th>
                        <th>Teacher Join Code</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    $stmt = $conn->prepare("SELECT * FROM School");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['school_id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['school_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['student_join_code']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['teacher_join_code']) . "</td>";
                            echo "<td><a class='icon-btn' href='edit-school.php?school_id=" . $row['school_id'] . "'>&#9881;</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No schools found</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<!-- lines 3-6 written by Caleb McHaney -->
<!-- lines 10, 12, 18-24, 26-30, 32-37, 39-43, 45-125, 127-129, 131-134, 136-142, 144-148, 150-221, 235-272 written by Jordan Munster -->
<!-- lines 1-2, 7-9, 11, 13-17, 25, 31, 38, 44, 126, 130, 135, 143, 149, 222-234 written by Benjamin Nguyen -->
