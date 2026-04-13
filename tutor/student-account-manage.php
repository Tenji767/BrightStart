<!--<?php

session_start();

$errorMsg = "";
$removedMsg = "";

// Just some sample students to show how the functionality works. This would be replaced with database calls.
if (!isset($_SESSION["students"]) || !is_array($_SESSION["students"])) {
    $_SESSION["students"] = [
        [
            "id" => "john_doe",
            "name" => "John Doe",
            "email" => "john.doe@example.com",
            "username" => "john_doe",
            "grade" => "5",
            "status" => "Active",
            "date_added" => "2026-02-21"
        ],
        [
            "id" => "jane_smith",
            "name" => "Jane Smith",
            "email" => "jane.smith@example.com",
            "username" => "jane_smith",
            "grade" => "6",
            "status" => "Active",
            "date_added" => "2026-02-22"
        ],
        [
            "id" => "maria_garcia",
            "name" => "Maria Garcia",
            "email" => "maria.garcia@example.com",
            "username" => "maria_garcia",
            "grade" => "4",
            "status" => "Active",
            "date_added" => "2026-02-20"
        ],
        [
            "id" => "liam_wilson",
            "name" => "Liam Wilson",
            "email" => "liam.wilson@example.com",
            "username" => "liam_wilson",
            "grade" => "8",
            "status" => "Active",
            "date_added" => "2026-02-19"
        ],
        [
            "id" => "ava_thompson",
            "name" => "Ava Thompson",
            "email" => "ava.thompson@example.com",
            "username" => "ava_thompson",
            "grade" => "K",
            "status" => "Active",
            "date_added" => "2026-02-18"
        ],
        [
            "id" => "ethan_clark",
            "name" => "Ethan Clark",
            "email" => "ethan.clark@example.com",
            "username" => "ethan_clark",
            "grade" => "11",
            "status" => "Active",
            "date_added" => "2026-02-23"
        ],
        [
            "id" => "sophia_lee",
            "name" => "Sophia Lee",
            "email" => "sophia.lee@example.com",
            "username" => "sophia_lee",
            "grade" => "7",
            "status" => "Active",
            "date_added" => "2026-02-24"
        ],
        [
            "id" => "noah_martin",
            "name" => "Noah Martin",
            "email" => "noah.martin@example.com",
            "username" => "noah_martin",
            "grade" => "2",
            "status" => "Active",
            "date_added" => "2026-02-16"
        ],
        [
            "id" => "olivia_hall",
            "name" => "Olivia Hall",
            "email" => "olivia.hall@example.com",
            "username" => "olivia_hall",
            "grade" => "9",
            "status" => "Active",
            "date_added" => "2026-02-15"
        ],
        [
            "id" => "jacob_adams",
            "name" => "Jacob Adams",
            "email" => "jacob.adams@example.com",
            "username" => "jacob_adams",
            "grade" => "12",
            "status" => "Active",
            "date_added" => "2026-02-14"
        ],
        [
            "id" => "Test_test123",
            "name" => "Test User",
            "email" => "test.user@example.com",
            "username" => "Test_test123",
            "grade" => "3",
            "status" => "Active",
            "date_added" => "2023-05-21"
        ],
    ];
}

// This is the remove button that will remove a student from the list (removing their account). It also will have a confirmation pop up to make sure the selected student for account deletion is correct and that the admin knows that this action cannot be undone. Once the database is good to go this would call the database to remove the student from the database.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["remove"])) {
        $selected = $_POST["selected_student"] ?? "";

        if ($selected === "") {
            $errorMsg = "Please select a student first, then click - Remove Student Account.";
        } else {
            $before = count($_SESSION["students"]);

            $_SESSION["students"] = array_values(array_filter(
                $_SESSION["students"],
                fn($s) => $s["id"] !== $selected
            ));

            $after = count($_SESSION["students"]);

            if ($after < $before) {
                $removedMsg = "Removed student account: " . htmlspecialchars($selected);
            } else {
                $errorMsg = "Student account was not found.";
            }
        }
    }
}

$students = $_SESSION["students"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Account Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <style>
        body{ font-family: Arial, Helvetica, sans-serif; margin: 24px; line-height: 1.35; }

        div.student-account-list{ display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px; }
        button{ padding: 10px 12px; cursor:pointer; }

        .msg{ padding: 10px 12px; border: 1px solid #ddd; background: #fafafa; border-radius: 8px; margin: 12px 0; }
        .msg.error{ border-color: #e7b3b3; background: #fff3f3; }
        .msg.ok{ border-color: #b7e3c2; background: #f3fff6; }

        .filters{
            display:grid;
            grid-template-columns: 1fr 220px 220px;
            gap:12px;
            align-items:end;
            margin: 10px 0 10px;
        }
        .filters label{ display:block; font-size: 13px; margin-bottom: 6px; }
        .filters input, .filters select{ width:100%; padding:10px; }

        .search-row{ display:flex; gap:10px; }
        .search-row input{ flex:1; }

        table.student-account-list{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        th, td{
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
            word-wrap: break-word;
        }
        th{ background:#f6f6f6; }

        th:nth-child(1), td:nth-child(1){ width: 70px; text-align:center; }
        th:nth-child(5), td:nth-child(5){ width: 70px; text-align:center; }
        th:nth-child(6), td:nth-child(6){ width: 90px; }
        th:nth-child(7), td:nth-child(7){ width: 120px; }
        th:nth-child(8), td:nth-child(8){ width: 220px; }

        .note{ font-size: 13px; color: #444; margin-top: 8px; }

        @media (max-width: 900px){
            .filters{ grid-template-columns: 1fr; }
            table.student-account-list{ table-layout:auto; }
        }
    </style>
</head>

<body>

<h1>Student Account Management</h1>

<br>
<!-- These are where the two buttons are located one being for removing a student account and another for creating a new student account. The Create Student Account button will take the admin to a page where they can type in the information of the student to be created. -->
<div class ="student-account-list">
    <a href="admin-student-account-create.php"><button type="button">+ Create Student Account</button></a>


    <button type="submit" name="remove" form="removeForm" id="removeBtn">- Remove Student Account</button>
</div>

<br>

<a href="admin-dashboard.php">
    <button type="button" class="admin-menu-item">Back to Dashboard page</button>
</a>

<br>

<!-- Used some of the same sorting startegy as the lesson management page, but with different options that would maybe fit the student account page. -->
<br>

<div class="filters">
    <div>
        <label for="student-search"><strong>Search (Name or Username)</strong></label>
        <div class="search-row">
            <input type="text" id="student-search" placeholder="Search..." />
            <button type="button" id="student-search-btn">Search</button>
        </div>
    </div>

    <div>
        <label for="grade-filter"><strong>Filter by Grade</strong></label>
        <select id="grade-filter">
            <option value="">All Grades</option>
            <option value="K">K</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>
    </div>

    <div>
        <label for="student-account-sort"><strong>Sort by Date Added</strong></label>
        <select id="student-account-sort">
            <option value="date-new" selected>Date Added (Newest)</option>
            <option value="date-old">Date Added (Oldest)</option>
        </select>
    </div>
</div>

<p class="note"><em>Professional note: passwords are not displayed. Admins reset credentials when needed.</em></p>
<div id="student-count" class="note"><em>Showing 0 students.</em></div>

<?php if ($errorMsg): ?>
    <div class="msg error"><strong><?php echo htmlspecialchars($errorMsg); ?></strong></div>
<?php endif; ?>

<?php if ($removedMsg): ?>
    <div class="msg ok"><strong><?php echo $removedMsg; ?></strong></div>
<?php endif; ?>

<form id="removeForm" action="student-account-manage.php" method="post">

<table class="student-account-list">
    <thead>
        <tr>
            <th>Select</th>
            <th>Student Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Grade</th>
            <th>Status</th>
            <th>Date Added</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody id="student-tbody">
        <?php foreach ($students as $s): ?>
        <tr class="student-row"
            data-date="<?php echo htmlspecialchars($s["date_added"]); ?>"
            data-grade="<?php echo htmlspecialchars($s["grade"]); ?>">
            <td>
                <input type="radio" name="selected_student" value="<?php echo htmlspecialchars($s["id"]); ?>">
            </td>
            <td><?php echo htmlspecialchars($s["name"]); ?></td>
            <td><?php echo htmlspecialchars($s["email"]); ?></td>
            <td><?php echo htmlspecialchars($s["username"]); ?></td>
            <td><?php echo htmlspecialchars($s["grade"]); ?></td>
            <td><?php echo htmlspecialchars($s["status"]); ?></td>
            <td><?php echo htmlspecialchars($s["date_added"]); ?></td>
            <td>
                <button type="button">Reset Password</button>
                <button type="button">Disable</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</form>

<script>
let ALL_STUDENT_ROWS = [];

function applyStudentFiltersAndSort() {
  const searchText = (document.getElementById("student-search").value || "").trim().toLowerCase();
  const gradeValue = (document.getElementById("grade-filter").value || "").trim();
  const sortMode = (document.getElementById("student-account-sort").value || "date-new").trim();

  const tbody = document.getElementById("student-tbody");

  const filtered = ALL_STUDENT_ROWS.filter((row) => {
    const name = (row.children[1]?.innerText || "").toLowerCase();      // Student Name
    const username = (row.children[3]?.innerText || "").toLowerCase();  // Username
    const rowGrade = row.dataset.grade || "";

    const matchesSearch = searchText === "" || name.includes(searchText) || username.includes(searchText);
    const matchesGrade = gradeValue === "" || rowGrade === gradeValue;

    return matchesSearch && matchesGrade;
  });

  filtered.sort((a, b) => {
    const da = Date.parse(a.dataset.date || "");
    const db = Date.parse(b.dataset.date || "");
    if (sortMode === "date-old") return da - db;
    return db - da;
  });

  const countEl = document.getElementById("student-count");
  if (countEl) countEl.innerHTML = "<em>Showing " + filtered.length + " students.</em>";

  tbody.innerHTML = "";
  filtered.forEach(r => tbody.appendChild(r));
}

document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById("student-tbody");
  ALL_STUDENT_ROWS = Array.from(tbody.querySelectorAll("tr.student-row"));

  document.getElementById("student-search").addEventListener("input", applyStudentFiltersAndSort);
  document.getElementById("grade-filter").addEventListener("change", applyStudentFiltersAndSort);
  document.getElementById("student-account-sort").addEventListener("change", applyStudentFiltersAndSort);
  document.getElementById("student-search-btn").addEventListener("click", applyStudentFiltersAndSort);

  applyStudentFiltersAndSort();

  // A simple pop up confirmation for when an admin is removing a students account.
  document.getElementById("removeForm").addEventListener("submit", (e) => {
    const selected = document.querySelector("input[name='selected_student']:checked");
    if (!selected) {
      // Let PHP also handle it, but this prevents an unnecessary submit
      alert("Please select a student first, then click - Remove Student Account.");
      e.preventDefault();
      return;
    }

    const ok = confirm("Are you sure you want to delete this student account? This cannot be undone.");
    if (!ok) e.preventDefault();
  });
});
</script>

</body>
</html>

<!-- by Noah Reynolds-->
