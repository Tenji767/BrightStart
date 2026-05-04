<?php
session_start();
$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'teacher' && $role !== 'admin')) {
    header("Location: ../login.php");
    exit();
}
// error_reporting(E_ALL);//display errors upon starting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
?>
<!DOCTYPE HTML>
<!-- standard head -->
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Lesson Management</title>
<link rel="stylesheet" href="tutor-style.css">
</head>

<body>



<div class="admin-header">
<h1 class="pagename">Manage Lessons</h1>
</div>

<div class="returnBox">
<a href="tutor-dashboard(notAI).php" class="returnBtn">To Dashboard</a>


</div>

<?php 
$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

}

$school_id = intval($_SESSION['school_id'] ?? 0);

// Get search, filter, and sort parameters
$search = $_GET['search'] ?? '';
$filter_teacher = $_GET['filter_teacher'] ?? '';
$filter_grade = $_GET['filter_grade'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'lesson_title';
$sort_order = $_GET['sort_order'] ?? 'ASC';

// Validate sort order
if (!in_array($sort_order, ['ASC', 'DESC'])) {
    $sort_order = 'ASC';
}

// Build SQL query with filters
$where_clauses = ["Lesson.school_id = ?"];
$bind_params = ["i", $school_id];

if (!empty($search)) {
    $where_clauses[] = "lesson_title LIKE ?";
    $bind_params[0] .= "s";
    $bind_params[] = "%{$search}%";
}

if (!empty($filter_teacher)) {
    $where_clauses[] = "Lesson.teacher_id = ?";
    $bind_params[0] .= "i";
    $bind_params[] = intval($filter_teacher);
}

if (!empty($filter_grade)) {
    $where_clauses[] = "grade_id = ?";
    $bind_params[0] .= "s";
    $bind_params[] = $filter_grade;
}

$where_sql = implode(" AND ", $where_clauses);
$query = "SELECT lesson_title, lesson_id, grade_id, teacher_name FROM Lesson 
          LEFT JOIN TeacherAccount ON Lesson.teacher_id = TeacherAccount.teacher_id 
          WHERE {$where_sql} 
          ORDER BY {$sort_by} {$sort_order}";

$stmt = $conn->prepare($query);

// Bind parameters dynamically
if (count($bind_params) > 1) {
    $types = array_shift($bind_params);
    $stmt->bind_param($types, ...$bind_params);
}
$stmt->execute();
$lessons_result = $stmt->get_result();
?>

<div id="filter-sort-search">
    <form method="GET" style="display: grid; gap: 15px;">
        <div>
            <div>
                <label for="search">Search Lesson</label>
                <input type="text" id="search" name="search" placeholder="Lesson name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div>
                <label for="filter_teacher">Filter by Teacher</label>
                <select id="filter_teacher" name="filter_teacher" >
                    <option value="">All Teachers</option>
                    <?php
                    $teacher_stmt = $conn->prepare("SELECT DISTINCT teacher_id, teacher_name FROM TeacherAccount 
                                                    WHERE teacher_id IN (SELECT teacher_id FROM Lesson WHERE school_id = ?) 
                                                    ORDER BY teacher_name");
                    $teacher_stmt->bind_param("i", $school_id);
                    $teacher_stmt->execute();
                    $teacher_result = $teacher_stmt->get_result();
                    while($row = $teacher_result->fetch_assoc()){
                        $selected = ($filter_teacher == $row['teacher_id']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['teacher_id']) . "' {$selected}>" . htmlspecialchars($row['teacher_name']) . "</option>";
                    }
                    $teacher_stmt->close();
                    ?>
                </select>
            </div>

            <div>
                <label for="filter_grade" >Filter by Grade</label>
                <select id="filter_grade" name="filter_grade" >
                    <option value="">All Grades</option>
                    <?php
                    $grade_stmt = $conn->prepare("SELECT DISTINCT grade_id FROM Lesson WHERE school_id = ? ORDER BY grade_id");
                    $grade_stmt->bind_param("i", $school_id);
                    $grade_stmt->execute();
                    $grade_result = $grade_stmt->get_result();
                    while($row = $grade_result->fetch_assoc()){
                        $selected = ($filter_grade == $row['grade_id']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['grade_id']) . "' {$selected}>" . htmlspecialchars($row['grade_id']) . "</option>";
                    }
                    $grade_stmt->close();
                    ?>
                </select>
            </div>

            <button type="submit" >Filter</button>
            <a href="tutor-manage-lessons.php" >Clear</a>
        </div>
    </form>
</div>

<div style="margin-bottom: 15px;">
    <label for="sort_by">Sort by:</label>
    <form method="GET" style="display: inline-flex; gap: 10px; align-items: center;">
        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <input type="hidden" name="filter_teacher" value="<?php echo htmlspecialchars($filter_teacher); ?>">
        <input type="hidden" name="filter_grade" value="<?php echo htmlspecialchars($filter_grade); ?>">
        
        <select id="sort_by" name="sort_by" onchange="this.form.submit()" >
            <option value="lesson_title" <?php echo ($sort_by === 'lesson_title') ? 'selected' : ''; ?>>Lesson Title</option>
            <option value="grade_id" <?php echo ($sort_by === 'grade_id') ? 'selected' : ''; ?>>Grade</option>
            <option value="teacher_name" <?php echo ($sort_by === 'teacher_name') ? 'selected' : ''; ?>>Teacher</option>
            <option value="lesson_id" <?php echo ($sort_by === 'lesson_id') ? 'selected' : ''; ?>>Lesson ID</option>
        </select>
        
        <select id="sort_order" name="sort_order" onchange="this.form.submit()" >
            <option value="ASC" <?php echo ($sort_order === 'ASC') ? 'selected' : ''; ?>>↑ Ascending</option>
            <option value="DESC" <?php echo ($sort_order === 'DESC') ? 'selected' : ''; ?>>↓ Descending</option>
        </select>
    </form>
</div>

<table id="lessonTable">
    <tr>
        <th>Lesson Title</th>
        <th>Lesson ID</th>
        <th>Grade</th>
        <th>Teacher</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>


<?php
// Display lessons from already-executed query above
while($row = $lessons_result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['lesson_title'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['lesson_id'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['grade_id'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['teacher_name'] ?? '') . "</td>";
    echo "<td><a href=\"tutor-lesson-edit.php?lesson_id=" . htmlspecialchars($row['lesson_id'] ?? '') . "\"><button type=\"button\">Edit</button></a></td>";
    echo "<td>
    <form method=\"POST\" action=\"delete-lesson.php\" onsubmit=\"return confirm('Delete this lesson?');\">
            <input type=\"hidden\" name=\"lesson_id\" value=\"" . htmlspecialchars($row['lesson_id'] ?? '') . "\">
            <button type=\"submit\">Delete</button>
        </form></td>";
    echo "</tr>";
}

// Show message if no lessons found
if ($lessons_result->num_rows === 0) {
    echo "<tr><td colspan='6' style='text-align: center; padding: 20px;'>No lessons found</td></tr>";
}
?>



</table>





</body>
</html>
<!-- lines 2-7, 18, 42, 174, 187, 198 written by Caleb McHaney -->
<!-- lines 30 written by Reba Ponniah -->
<!-- lines 1, 8-17, 19-29, 31-41, 43-173, 175-186, 188-197, 199-211 written by Benjamin Nguyen -->
