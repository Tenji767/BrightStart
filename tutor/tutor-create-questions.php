<?php
// Author: Caleb McHaney
// admin-create-questions.php is the main file for handling question creation
// Questions are multiple choice only

// Starts sessions and sets error reporting for debugging
session_start();
$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'teacher' && $role !== 'admin')) {
    header("Location: ../login.php");
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// connects to the database
include "../db_connect.php";

$msg = "";
$msg_type = ""; // "success" or "error"

//AJAX: load lessons by grade for the form dropdown, scoped to this tutor's school
if (isset($_GET['action']) && $_GET['action'] === 'get_lessons') {
    $grade_id = intval($_GET['grade_id']);
    $school_id = intval($_SESSION['school_id'] ?? 0);
    $stmt = $conn->prepare("SELECT lesson_id, lesson_title FROM Lesson WHERE grade_id = ? AND school_id = ? ORDER BY lesson_id ASC");
    $stmt->bind_param("ii", $grade_id, $school_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lessons = [];
    while ($row = $result->fetch_assoc()) $lessons[] = $row;
    header('Content-Type: application/json');
    echo json_encode($lessons);
    exit;
}

// Create Question Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_question'])) {
    $question_text  = trim($_POST['question_text'] ?? '');
    $option_a       = trim($_POST['option_a'] ?? '');
    $option_b       = trim($_POST['option_b'] ?? '');
    $option_c       = trim($_POST['option_c'] ?? '');
    $option_d       = trim($_POST['option_d'] ?? '');
    $correct_option = $_POST['correct_option'] ?? '';
    $lesson_id      = intval($_POST['lesson_id'] ?? 0);
    $grade_id       = intval($_POST['grade_id'] ?? 0);

    if (!$question_text || !$option_a || !$option_b || !$option_c || !$option_d || !$correct_option || !$lesson_id || !$grade_id) {
        $msg      = "All fields are required.";
        $msg_type = "error";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO Questions (question_text, option_a, option_b, option_c, option_d, correct_option, lesson_id, grade_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssssii", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $lesson_id, $grade_id);

        if ($stmt->execute()) {
            // Redirect after POST to prevent duplicate submission on refresh
            header("Location: tutor-create-questions.php?success=1");
            exit;
        } else {
            $msg      = "Error creating question: " . $stmt->error;
            $msg_type = "error";
        }
    }
}

// Delete Question Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
    $question_id = intval($_POST['question_id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM Questions WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);

    if ($stmt->execute()) {
        // Preserve the current lesson filter after delete
        $back = isset($_POST['filter_lesson_id']) && intval($_POST['filter_lesson_id'])
            ? '&filter_lesson_id=' . intval($_POST['filter_lesson_id'])
            : '';
        header("Location: tutor-create-questions.php?deleted=1" . $back);
        exit;
    } else {
        $msg      = "Error deleting question: " . $stmt->error;
        $msg_type = "error";
    }
}

// Pick up success messages from redirects
if (isset($_GET['success'])) { $msg = "Question created successfully."; $msg_type = "success"; }
if (isset($_GET['deleted'])) { $msg = "Question deleted successfully.";  $msg_type = "success"; }

// Load Grades for the create form dropdown
$grades = [];
$grade_result = $conn->query("SELECT grade_id, grade_name FROM Grade ORDER BY grade_id ASC");
if ($grade_result) {
    while ($row = $grade_result->fetch_assoc()) $grades[] = $row;
}

// Load lessons for the filter dropdown, scoped to this tutor's school
$all_lessons = [];
$school_id = intval($_SESSION['school_id'] ?? 0);
$lesson_filter_stmt = $conn->prepare("SELECT lesson_id, lesson_title FROM Lesson WHERE school_id = ? ORDER BY lesson_id ASC");
$lesson_filter_stmt->bind_param("i", $school_id);
$lesson_filter_stmt->execute();
$all_lesson_result = $lesson_filter_stmt->get_result();
if ($all_lesson_result) {
    while ($row = $all_lesson_result->fetch_assoc()) $all_lessons[] = $row;
}

// Load all questions for admin view, filtered by lesson if selected
$filter_lesson_id = intval($_GET['filter_lesson_id'] ?? 0);
$all_questions = [];

if ($filter_lesson_id) {
    $stmt = $conn->prepare(
        "SELECT q.*, l.lesson_title, g.grade_name
         FROM Questions q
         JOIN Lesson l ON q.lesson_id = l.lesson_id
         JOIN Grade  g ON q.grade_id  = g.grade_id
         WHERE q.lesson_id = ? AND l.school_id = ?
         ORDER BY q.question_id ASC"
    );
    $stmt->bind_param("ii", $filter_lesson_id, $school_id);
} else {
    $stmt = $conn->prepare(
        "SELECT q.*, l.lesson_title, g.grade_name
         FROM Questions q
         JOIN Lesson l ON q.lesson_id = l.lesson_id
         JOIN Grade  g ON q.grade_id  = g.grade_id
         WHERE l.school_id = ?
         ORDER BY g.grade_id ASC, q.lesson_id ASC, q.question_id ASC"
    );
    $stmt->bind_param("i", $school_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $all_questions[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Questions – BrightStart Admin</title>
    <link rel="stylesheet" href="tutor-style.css">
    <link rel="stylesheet" href="admin-create-questions.css">
</head>

<body>

<div class="admin-header">
    <h1 class="pagename">Create Questions</h1>
</div>

<div class="returnBox">
    <a href="tutor-dashboard.php" class="returnBtn">To Dashboard</a>
</div>

<main>

    <!-- Status message -->
    <?php if ($msg): ?>
        <div class="msg <?= $msg_type ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <!-- ── Create Question Form ── -->
    <div class="card">
        <h2>Add a New Question</h2>
        <form method="POST" action="tutor-create-questions.php">

            <!-- Grade + Lesson dropdowns -->
            <div class="form-row">
                <div class="form-group">
                    <label for="grade_id">Grade</label>
                    <select name="grade_id" id="grade_id" required>
                        <option value="">-- Select a Grade --</option>
                        <?php foreach ($grades as $grade): ?>
                            <option value="<?= $grade['grade_id'] ?>">
                                <?= htmlspecialchars($grade['grade_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lesson_id">Lesson</label>
                    <select name="lesson_id" id="lesson_id" required>
                        <option value="">-- Select a Grade First --</option>
                    </select>
                </div>
            </div>

            <!-- Question text -->
            <div class="form-group" style="margin-bottom:18px;">
                <label for="question_text">Question</label>
                <textarea name="question_text" id="question_text" rows="3"
                    placeholder="Enter the question text here..." required></textarea>
            </div>

            <!-- Answer options A–D -->
            <div class="options-grid">
                <div class="form-group">
                    <label>Option A</label>
                    <input type="text" name="option_a" placeholder="Enter option A" required>
                </div>
                <div class="form-group">
                    <label>Option B</label>
                    <input type="text" name="option_b" placeholder="Enter option B" required>
                </div>
                <div class="form-group">
                    <label>Option C</label>
                    <input type="text" name="option_c" placeholder="Enter option C" required>
                </div>
                <div class="form-group">
                    <label>Option D</label>
                    <input type="text" name="option_d" placeholder="Enter option D" required>
                </div>
            </div>

            <!-- Correct answer -->
            <div class="form-group" style="max-width:200px; margin-bottom:22px;">
                <label for="correct_option">Correct Answer</label>
                <select name="correct_option" id="correct_option" required>
                    <option value="">-- Select --</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <button type="submit" name="create_question" class="btn-submit">Add Question</button>

        </form>
    </div>

    <!-- ── Question List ── -->
    <div class="card">
        <h2>All Questions</h2>

        <!-- Filter by lesson -->
        <form method="GET" action="tutor-create-questions.php" class="filter-bar">
            <label for="filter_lesson_id">Filter by Lesson:</label>
            <select name="filter_lesson_id" id="filter_lesson_id" onchange="this.form.submit()">
                <option value="">-- All Lessons --</option>
                <?php foreach ($all_lessons as $lesson): ?>
                    <option value="<?= $lesson['lesson_id'] ?>"
                        <?= ($filter_lesson_id === $lesson['lesson_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lesson['lesson_title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($filter_lesson_id): ?>
                <a href="tutor-create-questions.php">Clear filter</a>
            <?php endif; ?>
        </form>

        <?php if (empty($all_questions)): ?>
            <p class="empty-state">No questions found.</p>
        <?php else: ?>
            <?php foreach ($all_questions as $row): ?>
                <div class="question-card">
                    <p class="breadcrumb">
                        <?= htmlspecialchars($row['grade_name']) ?> &rsaquo;
                        <?= htmlspecialchars($row['lesson_title']) ?>
                    </p>
                    <p class="question-text"><?= htmlspecialchars($row['question_text']) ?></p>
                    <ul class="options-list">
                        <?php foreach (['A','B','C','D'] as $opt): ?>
                            <li class="<?= $row['correct_option'] === $opt ? 'correct' : '' ?>">
                                <strong><?= $opt ?>:</strong>
                                <?= htmlspecialchars($row['option_' . strtolower($opt)]) ?>
                                <?= $row['correct_option'] === $opt ? ' &#10003;' : '' ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <form method="POST" action="tutor-create-questions.php"
                          onsubmit="return confirm('Delete this question?');">
                        <input type="hidden" name="question_id" value="<?= $row['question_id'] ?>">
                        <input type="hidden" name="filter_lesson_id" value="<?= $filter_lesson_id ?>">
                        <button type="submit" name="delete_question" class="btn-delete">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</main>

</body>

<!-- JS: populate lesson dropdown when grade is selected -->
<script>
    document.getElementById('grade_id').addEventListener('change', function () {
        const gradeId = this.value;
        const lessonSelect = document.getElementById('lesson_id');
        lessonSelect.innerHTML = '<option value="">Loading...</option>';

        if (!gradeId) {
            lessonSelect.innerHTML = '<option value="">-- Select a Grade First --</option>';
            return;
        }

        // Fetch lessons for the selected grade and populate the lesson dropdown
        fetch(`tutor-create-questions.php?action=get_lessons&grade_id=${gradeId}`)
            .then(r => r.json())
            .then(lessons => {
                lessonSelect.innerHTML = '<option value="">-- Select a Lesson --</option>';
                if (!lessons.length) {
                    lessonSelect.innerHTML = '<option value="">No lessons for this grade</option>';
                    return;
                }
                lessons.forEach(l => {
                    const o = document.createElement('option');
                    o.value = l.lesson_id;
                    o.textContent = l.lesson_title;
                    lessonSelect.appendChild(o);
                });
            })
            .catch(() => lessonSelect.innerHTML = '<option value="">Error loading lessons</option>');
    });
</script>
<!-- lines 1-329 written by Caleb McHaney -->
</html>
