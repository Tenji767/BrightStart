<?php
// Author: Caleb McHaney
// Allows tutors to view lesson and quiz history for students at their school
session_start();
include "../db_connect.php";

$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'teacher' && $role !== 'admin')) {
    header("Location: ../login.php");
    exit;
}

$school_id           = intval($_SESSION['school_id']);
$selected_student_id = intval($_GET['student_id'] ?? 0);
$attempt_id          = intval($_GET['attempt_id'] ?? 0);

$st_stmt = $conn->prepare(
    "SELECT s.student_id, s.student_name, s.email, g.grade_name
     FROM StudentAccount s JOIN Grade g ON g.grade_id = s.grade_id
     WHERE s.school_id = ? ORDER BY s.student_name ASC"
);
$st_stmt->bind_param("i", $school_id);
$st_stmt->execute();
$all_students = $st_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$selected_student = null;
$lesson_history   = [];
$quiz_attempts    = [];
$attempt_detail   = null;
$attempt_answers  = [];

if ($selected_student_id) {
    $ver = $conn->prepare(
        "SELECT s.student_id, s.student_name, s.email, g.grade_name
         FROM StudentAccount s JOIN Grade g ON g.grade_id = s.grade_id
         WHERE s.student_id = ? AND s.school_id = ?"
    );
    $ver->bind_param("ii", $selected_student_id, $school_id);
    $ver->execute();
    $selected_student = $ver->get_result()->fetch_assoc();

    if ($selected_student) {
        $lh = $conn->prepare(
            "SELECT lh.first_viewed_at, lh.last_viewed_at, lh.view_count, l.lesson_id, l.lesson_title, l.grade_id
             FROM LessonHistory lh JOIN Lesson l ON l.lesson_id = lh.lesson_id
             WHERE lh.student_id = ? ORDER BY lh.last_viewed_at DESC"
        );
        $lh->bind_param("i", $selected_student_id);
        $lh->execute();
        $lesson_history = $lh->get_result()->fetch_all(MYSQLI_ASSOC);

        $qa = $conn->prepare(
            "SELECT qa.attempt_id, qa.score, qa.total_questions, qa.attempted_at, l.lesson_title, l.lesson_id
             FROM QuizAttempts qa JOIN Lesson l ON l.lesson_id = qa.lesson_id
             WHERE qa.user_id = ? ORDER BY qa.attempted_at DESC"
        );
        $qa->bind_param("i", $selected_student_id);
        $qa->execute();
        $quiz_attempts = $qa->get_result()->fetch_all(MYSQLI_ASSOC);

        if ($attempt_id) {
            $det = $conn->prepare(
                "SELECT qa.attempt_id, qa.score, qa.total_questions, qa.attempted_at, l.lesson_title, l.lesson_id
                 FROM QuizAttempts qa JOIN Lesson l ON l.lesson_id = qa.lesson_id
                 WHERE qa.attempt_id = ? AND qa.user_id = ?"
            );
            $det->bind_param("ii", $attempt_id, $selected_student_id);
            $det->execute();
            $attempt_detail = $det->get_result()->fetch_assoc();

            if ($attempt_detail) {
                $ans = $conn->prepare(
                    "SELECT qaa.chosen_option, qaa.correct_option, qaa.is_correct, qaa.used_ai, qaa.skipped,
                            q.question_id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d
                     FROM QuizAttemptAnswers qaa JOIN Questions q ON q.question_id = qaa.question_id
                     WHERE qaa.attempt_id = ? ORDER BY q.question_id ASC"
                );
                $ans->bind_param("i", $attempt_id);
                $ans->execute();
                $attempt_answers = $ans->get_result()->fetch_all(MYSQLI_ASSOC);
            }
        }
    }
}

$total_lessons  = count($lesson_history);
$total_attempts = count($quiz_attempts);
$avg_pct = 0;
if ($total_attempts > 0) {
    $avg_pct = round(array_sum(array_map(
        fn($a) => ($a['score'] / max($a['total_questions'], 1)) * 100, $quiz_attempts
    )) / $total_attempts);
}

$sn = $conn->prepare("SELECT school_name FROM School WHERE school_id = ?");
$sn->bind_param("i", $school_id);
$sn->execute();
$sn_row = $sn->get_result()->fetch_assoc();
$school_name = $sn_row['school_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Progress &mdash; BrightStart</title>
    <link rel="stylesheet" href="tutor-style.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #0891b2;
            --text-primary: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
            --shadow: 0 4px 6px rgba(0,0,0,0.08);
        }
        .page-wrap { max-width: 1100px; margin: 0 auto; padding: 24px 20px 48px; }
        .section-title { font-size: 1.15em; font-weight: 700; color: var(--text-primary); margin: 0 0 14px; }
        .selector-filters { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; align-items: flex-end; }
        .student-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
        .student-card-btn {
            display: block; padding: 12px 14px; background: #f8fafc;
            border: 2px solid var(--border-color); border-radius: 10px;
            cursor: pointer; text-decoration: none; transition: all 0.18s ease;
        }
        .student-card-btn:hover, .student-card-btn.active { border-color: var(--primary-color); background: #eff6ff; }
        .student-card-btn.active { box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
        .student-card-name { font-weight: 700; font-size: 0.95em; color: var(--text-primary); }
        .student-card-meta { font-size: 0.8em; color: var(--text-light); margin-top: 3px; }
        .student-detail-header {
            background: linear-gradient(135deg, rgba(37,99,235,0.08) 0%, rgba(8,145,178,0.08) 100%);
            border-left: 5px solid var(--primary-color); border-radius: 12px;
            padding: 20px 24px; margin-bottom: 20px; box-shadow: var(--shadow);
            display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;
        }
        .student-detail-header h2 { font-size: 1.4em; color: var(--text-primary); margin-bottom: 4px; }
        .student-detail-header p { color: var(--text-light); font-size: 0.9em; margin: 0; }
        .dashboard-stats { width: auto; height: auto; }
        .tab-bar { display: flex; gap: 8px; margin-bottom: 18px; }
        .tab-btn {
            padding: 9px 22px; border-radius: 8px; border: 2px solid var(--border-color);
            background: white; font-size: 14px; font-weight: 600; color: var(--text-light);
            cursor: pointer; transition: all 0.18s;
        }
        .tab-btn.active { background: var(--primary-color); border-color: var(--primary-color); color: white; }
        .tab-btn:hover:not(.active) { border-color: var(--primary-color); color: var(--primary-color); }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .history-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; }
        .history-card {
            background: white; border-radius: 12px; padding: 16px 20px; box-shadow: var(--shadow);
            display: flex; justify-content: space-between; align-items: center;
            gap: 12px; flex-wrap: wrap; border: 1px solid var(--border-color);
        }
        .history-card-left { display: flex; flex-direction: column; gap: 4px; }
        .history-lesson-title { font-size: 1em; font-weight: 700; color: var(--text-primary); }
        .history-meta { font-size: 0.82em; color: var(--text-light); }
        .history-card-right { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .score-badge { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
        .score-value { font-size: 1.05em; font-weight: 700; color: var(--text-primary); }
        .view-count-badge { font-size: 1.05em; font-weight: 700; color: var(--text-primary); }
        .pct-pill { font-size: 0.72em; font-weight: 700; padding: 2px 8px; border-radius: 99px; text-transform: uppercase; }
        .pct-high { background: #dcfce7; color: #166534; }
        .pct-mid  { background: #fef9c3; color: #854d0e; }
        .pct-low  { background: #fee2e2; color: #991b1b; }
        .btn-secondary {
            display: inline-block; padding: 8px 18px; background: white;
            color: var(--primary-color); border: 2px solid var(--primary-color);
            border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.18s;
        }
        .btn-secondary:hover { background: #eff6ff; }
        .empty-state {
            background: white; border-radius: 12px; padding: 36px 24px; text-align: center;
            box-shadow: var(--shadow); color: var(--text-light); font-size: 0.95em; border: 1px solid var(--border-color);
        }
        .attempt-back-bar { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .results-hero {
            background: white; border-radius: 12px; padding: 28px 24px; text-align: center;
            box-shadow: var(--shadow); margin-bottom: 24px; border: 1px solid var(--border-color);
        }
        .results-hero h2 { color: var(--text-primary); margin-bottom: 10px; }
        .score-big { font-size: 2.8em; font-weight: 800; color: var(--primary-color); }
        .score-pct { font-size: 1.1em; color: var(--text-light); margin-top: 4px; }
        .review-list { display: flex; flex-direction: column; gap: 14px; margin-bottom: 28px; }
        .review-item {
            background: white; border-radius: 12px; padding: 18px 20px;
            box-shadow: var(--shadow); border: 1px solid var(--border-color); border-left: 5px solid var(--border-color);
        }
        .review-item.correct  { border-left: 5px solid #22c55e; }
        .review-item.incorrect { border-left: 5px solid #ef4444; }
        .review-item.skipped  { border-left: 5px solid #f59e0b; }
        .review-item-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
        .review-q-text { font-weight: 600; color: var(--text-primary); font-size: 0.97em; flex: 1; }
        .review-badge { display: inline-block; font-size: 0.72em; font-weight: 700; padding: 3px 9px; border-radius: 99px; text-transform: uppercase; margin-left: 4px; }
        .badge-correct  { background: #dcfce7; color: #166534; }
        .badge-incorrect { background: #fee2e2; color: #991b1b; }
        .badge-skipped  { background: #fef9c3; color: #854d0e; }
        .badge-ai       { background: #e0e7ff; color: #3730a3; }
        .review-options { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; }
        .review-options li { padding: 8px 12px; border-radius: 8px; font-size: 0.88em; color: var(--text-primary); background: #f8fafc; border: 1px solid var(--border-color); }
        .opt-correct      { background: #dcfce7; border-color: #86efac; color: #166534; font-weight: 600; }
        .opt-wrong-chosen { background: #fee2e2; border-color: #fca5a5; color: #991b1b; font-weight: 600; }
        @media (max-width: 640px) {
            .student-detail-header, .history-card { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<div class="admin-header">
    <h1 class="pagename">Student Progress</h1>
    <h2><?= htmlspecialchars($school_name) ?></h2>
    <h2><a href="tutor-dashboard.php">Dashboard</a></h2>
</div>

<div class="page-wrap">

<?php if ($attempt_detail && $selected_student): ?>

    <div class="attempt-back-bar">
        <div class="returnBox">
            <a href="tutor-student-progress.php?student_id=<?= $selected_student_id ?>" class="returnBtn">&larr; Back</a>
        </div>
        <span style="color:var(--text-light);font-size:0.9em;">
            Viewing quiz attempt for <strong><?= htmlspecialchars($selected_student['student_name']) ?></strong>
        </span>
    </div>

    <div class="results-hero">
        <h2><?= htmlspecialchars($attempt_detail['lesson_title']) ?></h2>
        <p style="color:var(--text-light);font-size:0.9em;margin-bottom:12px;">
            Attempted on <?= date('F j, Y \a\t g:i A', strtotime($attempt_detail['attempted_at'])) ?>
        </p>
        <div class="score-big"><?= $attempt_detail['score'] ?> / <?= $attempt_detail['total_questions'] ?></div>
        <div class="score-pct"><?= round(($attempt_detail['score'] / max($attempt_detail['total_questions'], 1)) * 100) ?>% correct</div>
    </div>

    <p class="section-title">Question Review</p>

    <?php if (empty($attempt_answers)): ?>
        <div class="empty-state"><p>No answer data available for this attempt.</p></div>
    <?php else: ?>
        <div class="review-list">
        <?php foreach ($attempt_answers as $i => $ans):
            $is_correct  = (bool)$ans['is_correct'];
            $was_skipped = (bool)($ans['skipped'] ?? false);
            $chosen      = $ans['chosen_option'] ?? '';
            $correct     = $ans['correct_option'];
            $used_ai     = (bool)$ans['used_ai'];
            $options     = ['A' => $ans['option_a'], 'B' => $ans['option_b'], 'C' => $ans['option_c'], 'D' => $ans['option_d']];
            $item_class  = $is_correct ? 'correct' : ($was_skipped ? 'skipped' : 'incorrect');
        ?>
            <div class="review-item <?= $item_class ?>">
                <div class="review-item-header">
                    <span class="review-q-text">Q<?= $i + 1 ?>: <?= htmlspecialchars($ans['question_text']) ?></span>
                    <span>
                        <?php if ($was_skipped): ?>
                            <span class="review-badge badge-skipped">Skipped</span>
                        <?php else: ?>
                            <span class="review-badge <?= $is_correct ? 'badge-correct' : 'badge-incorrect' ?>">
                                <?= $is_correct ? '&#10003; Correct' : '&#10007; Incorrect' ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($used_ai): ?><span class="review-badge badge-ai">AI Help</span><?php endif; ?>
                    </span>
                </div>
                <ul class="review-options">
                    <?php foreach ($options as $letter => $text):
                        $cls = '';
                        if ($letter === strtoupper($correct)) $cls = 'opt-correct';
                        elseif ($letter === strtoupper($chosen) && !$is_correct) $cls = 'opt-wrong-chosen';
                    ?>
                        <li class="<?= $cls ?>">
                            <strong><?= $letter ?>:</strong> <?= htmlspecialchars($text) ?>
                            <?php if ($letter === strtoupper($correct)): ?> &mdash; Correct answer<?php endif; ?>
                            <?php if ($letter === strtoupper($chosen) && !$is_correct): ?> &mdash; Student's answer<?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php else: ?>

    <div id="filter-sort-search">
        <p class="section-title">Select a Student</p>
        <div class="selector-filters">
            <div>
                <label for="student-search">Search by name</label>
                <input type="text" id="student-search" placeholder="Type to filter&hellip;">
            </div>
            <div>
                <label for="grade-filter">Filter by grade</label>
                <select id="grade-filter">
                    <option value="">All Grades</option>
                    <?php
                    $grades_seen = [];
                    foreach ($all_students as $s) {
                        $g = htmlspecialchars($s['grade_name']);
                        if (!in_array($g, $grades_seen)) {
                            $grades_seen[] = $g;
                            $sel = ($selected_student && $selected_student['grade_name'] === $s['grade_name']) ? ' selected' : '';
                            echo "<option value=\"$g\"$sel>Grade $g</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php if (empty($all_students)): ?>
            <p style="color:var(--text-light);font-style:italic;">No students are enrolled at your school yet.</p>
        <?php else: ?>
            <div class="student-grid" id="student-grid">
                <?php foreach ($all_students as $s): ?>
                <a href="tutor-student-progress.php?student_id=<?= (int)$s['student_id'] ?>"
                   class="student-card-btn <?= ($selected_student_id === (int)$s['student_id']) ? 'active' : '' ?>"
                   data-name="<?= strtolower(htmlspecialchars($s['student_name'])) ?>"
                   data-grade="<?= htmlspecialchars($s['grade_name']) ?>">
                    <div class="student-card-name"><?= htmlspecialchars($s['student_name']) ?></div>
                    <div class="student-card-meta">Grade <?= htmlspecialchars($s['grade_name']) ?> &bull; <?= htmlspecialchars($s['email']) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($selected_student): ?>

    <div class="student-detail-header">
        <div>
            <h2><?= htmlspecialchars($selected_student['student_name']) ?></h2>
            <p>Grade <?= htmlspecialchars($selected_student['grade_name']) ?> &bull; <?= htmlspecialchars($selected_student['email']) ?></p>
        </div>
        <div class="dashboard-stats">
            <div class="stat-box"><p>Lessons Viewed</p><h2><?= $total_lessons ?></h2></div>
            <div class="stat-box"><p>Quiz Attempts</p><h2><?= $total_attempts ?></h2></div>
            <div class="stat-box"><p>Avg. Score</p><h2><?= $total_attempts > 0 ? $avg_pct . '%' : '&mdash;' ?></h2></div>
        </div>
    </div>

    <div class="tab-bar">
        <button class="tab-btn active" data-tab="lessons">Lesson History</button>
        <button class="tab-btn" data-tab="quizzes">Quiz History</button>
    </div>

    <div class="tab-panel active" id="tab-lessons">
        <?php if (empty($lesson_history)): ?>
            <div class="empty-state"><p><?= htmlspecialchars($selected_student['student_name']) ?> has not viewed any lessons yet.</p></div>
        <?php else: ?>
            <div class="history-list">
            <?php foreach ($lesson_history as $row): ?>
                <div class="history-card">
                    <div class="history-card-left">
                        <div class="history-lesson-title"><?= htmlspecialchars($row['lesson_title']) ?></div>
                        <div class="history-meta">First viewed: <?= date('M j, Y \a\t g:i A', strtotime($row['first_viewed_at'])) ?></div>
                        <div class="history-meta">Last viewed: <?= date('M j, Y \a\t g:i A', strtotime($row['last_viewed_at'])) ?></div>
                    </div>
                    <div class="history-card-right">
                        <div class="view-count-badge"><?= (int)$row['view_count'] ?> <?= $row['view_count'] == 1 ? 'view' : 'views' ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="tab-panel" id="tab-quizzes">
        <?php if (empty($quiz_attempts)): ?>
            <div class="empty-state"><p><?= htmlspecialchars($selected_student['student_name']) ?> has not completed any quizzes yet.</p></div>
        <?php else: ?>
            <div class="history-list">
            <?php foreach ($quiz_attempts as $row):
                $pct = round(($row['score'] / max($row['total_questions'], 1)) * 100);
                $pct_class = $pct >= 80 ? 'pct-high' : ($pct >= 50 ? 'pct-mid' : 'pct-low');
            ?>
                <div class="history-card">
                    <div class="history-card-left">
                        <div class="history-lesson-title"><?= htmlspecialchars($row['lesson_title']) ?></div>
                        <div class="history-meta"><?= date('M j, Y \a\t g:i A', strtotime($row['attempted_at'])) ?></div>
                    </div>
                    <div class="history-card-right">
                        <div class="score-badge">
                            <span class="score-value"><?= $row['score'] ?> / <?= $row['total_questions'] ?></span>
                            <span class="pct-pill <?= $pct_class ?>"><?= $pct ?>%</span>
                        </div>
                        <a href="tutor-student-progress.php?student_id=<?= $selected_student_id ?>&attempt_id=<?= (int)$row['attempt_id'] ?>"
                           class="btn-secondary">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php else: ?>
        <div class="empty-state"><p>Select a student above to view their lesson and quiz history.</p></div>
    <?php endif; ?>

<?php endif; ?>

</div>

<footer style="text-align:center;padding:24px;color:var(--text-light);font-size:0.85em;">
    &copy; <?= date('Y') ?> BrightStart Math Tutoring. All rights reserved.
</footer>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});

const searchInput  = document.getElementById('student-search');
const gradeSelect  = document.getElementById('grade-filter');
const studentCards = document.querySelectorAll('.student-card-btn');

function filterStudents() {
    const query = (searchInput?.value || '').trim().toLowerCase();
    const grade = (gradeSelect?.value || '').trim();
    studentCards.forEach(card => {
        const nameMatch  = !query || card.dataset.name.includes(query);
        const gradeMatch = !grade || card.dataset.grade === grade;
        card.style.display = (nameMatch && gradeMatch) ? '' : 'none';
    });
}

searchInput?.addEventListener('input', filterStudents);
gradeSelect?.addEventListener('change', filterStudents);
</script>

</body>
<!-- Lines 1-440 by Caleb McHaney -->
</html>
