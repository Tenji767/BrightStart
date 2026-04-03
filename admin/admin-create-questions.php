<?php
// Author: Caleb McHaney
// admin-create-questions.php is the main file for handling question creation
// Questions are multiple choice only

// Starts sessions and sets error reporting for debugging
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// connects to the database
include "../db_connect.php";

$msg = "";
$msg_type = ""; // "success" or "error"

//AJAX: load lessons by grade for the form dropdown
if (isset($_GET['action']) && $_GET['action'] === 'get_lessons') {
    $grade_id = intval($_GET['grade_id']);
    $stmt = $conn->prepare("SELECT lesson_id, lesson_title FROM Lesson WHERE grade_id = ? ORDER BY lesson_id ASC");
    $stmt->bind_param("i", $grade_id);
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
            header("Location: admin-create-questions.php?success=1");
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
        header("Location: admin-create-questions.php?deleted=1" . $back);
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

// Load ALL Lessons for the filter dropdown
$all_lessons = [];
$all_lesson_result = $conn->query("SELECT lesson_id, lesson_title FROM Lesson ORDER BY lesson_id ASC");
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
         WHERE q.lesson_id = ?
         ORDER BY q.question_id ASC"
    );
    $stmt->bind_param("i", $filter_lesson_id);
} else {
    $stmt = $conn->prepare(
        "SELECT q.*, l.lesson_title, g.grade_name
         FROM Questions q
         JOIN Lesson l ON q.lesson_id = l.lesson_id
         JOIN Grade  g ON q.grade_id  = g.grade_id
         ORDER BY g.grade_id ASC, q.lesson_id ASC, q.question_id ASC"
    );
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $all_questions[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../includes/header.php'); ?>
    <!-- Stylesheets need to be added still 3/24/26 CM -->
</head>

<body>

    <main>

        <h2>Add Question</h2>

        <!-- Status message -->
        <?php if ($msg): ?>
            <p style="color: <?= $msg_type === 'error' ? 'red' : 'green' ?>;">
                <?= htmlspecialchars($msg) ?>
            </p>
        <?php endif; ?>

        <!-- Create Question Form -->
        <form method="POST" action="admin-create-questions.php">

            <!-- Grade dropdown — lessons load based on this selection -->
            <label for="grade_id">Grade:
                <select name="grade_id" id="grade_id" required>
                    <option value="">-- Select a Grade --</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade['grade_id'] ?>">
                            <?= htmlspecialchars($grade['grade_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            &nbsp;

            <!-- Lesson dropdown — populated by JS when grade is picked -->
            <label for="lesson_id">Lesson:
                <select name="lesson_id" id="lesson_id" required>
                    <option value="">-- Select a Grade First --</option>
                </select>
            </label>

            <br><br>

            <label for="question_text">Question:<br>
                <textarea name="question_text" id="question_text" rows="3" cols="60"
                    placeholder="Enter question text..." required></textarea>
            </label>

            <br><br>

            <label>A: <input type="text" name="option_a" placeholder="Option A" size="40" required></label><br>
            <label>B: <input type="text" name="option_b" placeholder="Option B" size="40" required></label><br>
            <label>C: <input type="text" name="option_c" placeholder="Option C" size="40" required></label><br>
            <label>D: <input type="text" name="option_d" placeholder="Option D" size="40" required></label>

            <br><br>

            <label for="correct_option">Correct Answer:
                <select name="correct_option" id="correct_option" required>
                    <option value="">--</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </label>

            <br><br>

            <button type="submit" name="create_question">Add Question</button>

        </form>

        <hr>

        <!-- All Question View -->
        <h2>All Questions</h2>

        <!-- Filter by lesson -->
        <form method="GET" action="admin-create-questions.php">
            <label for="filter_lesson_id">Filter by Lesson:
                <select name="filter_lesson_id" id="filter_lesson_id" onchange="this.form.submit()">
                    <option value="">-- All Lessons --</option>
                    <?php foreach ($all_lessons as $lesson): ?>
                        <option value="<?= $lesson['lesson_id'] ?>"
                            <?= ($filter_lesson_id === $lesson['lesson_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lesson['lesson_title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if ($filter_lesson_id): ?>
                &nbsp;<a href="admin-create-questions.php">Clear filter</a>
            <?php endif; ?>
        </form>

        <br>

        <?php if (empty($all_questions)): ?>
            <p>No questions found.</p>
        <?php else: ?>
            <?php foreach ($all_questions as $row): ?>
                <div class="question">
                    <p><small><?= htmlspecialchars($row['grade_name']) ?> &rsaquo; <?= htmlspecialchars($row['lesson_title']) ?></small></p>
                    <p><strong><?= htmlspecialchars($row['question_text']) ?></strong></p>
                    <ul>
                        <li <?= $row['correct_option'] === 'A' ? 'style="font-weight:bold;"' : '' ?>>
                            A: <?= htmlspecialchars($row['option_a']) ?>
                            <?= $row['correct_option'] === 'A' ? ' ✓' : '' ?>
                        </li>
                        <li <?= $row['correct_option'] === 'B' ? 'style="font-weight:bold;"' : '' ?>>
                            B: <?= htmlspecialchars($row['option_b']) ?>
                            <?= $row['correct_option'] === 'B' ? ' ✓' : '' ?>
                        </li>
                        <li <?= $row['correct_option'] === 'C' ? 'style="font-weight:bold;"' : '' ?>>
                            C: <?= htmlspecialchars($row['option_c']) ?>
                            <?= $row['correct_option'] === 'C' ? ' ✓' : '' ?>
                        </li>
                        <li <?= $row['correct_option'] === 'D' ? 'style="font-weight:bold;"' : '' ?>>
                            D: <?= htmlspecialchars($row['option_d']) ?>
                            <?= $row['correct_option'] === 'D' ? ' ✓' : '' ?>
                        </li>
                    </ul>
                    <form method="POST" action="admin-create-questions.php"
                          onsubmit="return confirm('Delete this question?');">
                        <input type="hidden" name="question_id" value="<?= $row['question_id'] ?>">
                        <!-- Preserve the current filter after delete -->
                        <input type="hidden" name="filter_lesson_id" value="<?= $filter_lesson_id ?>">
                        <button type="submit" name="delete_question">Delete</button>
                    </form>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>

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

        fetch(`admin-create-questions.php?action=get_lessons&grade_id=${gradeId}`)
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

</html>
