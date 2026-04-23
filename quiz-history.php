<?php
//Author: Caleb McHaney
//quiz-history.php allows students to view all of their previous quiz attempts and review per-question answers

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include "db_connect.php";

$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'student' && $role !== 'teacher' && $role !== 'admin')) {
    header("Location: login.php");
    exit;
}

$user_id    = intval($_SESSION['user_id']);
$attempt_id = intval($_GET['attempt_id'] ?? 0);

$attempt = null;
$answers = [];

//if an attempt id was provided load the detail view for that attempt
if ($attempt_id) {
    $stmt = $conn->prepare(
        "SELECT qa.attempt_id, qa.score, qa.total_questions, qa.attempted_at,
                l.lesson_title, l.lesson_id
         FROM QuizAttempts qa
         JOIN Lesson l ON l.lesson_id = qa.lesson_id
         WHERE qa.attempt_id = ? AND qa.user_id = ?"
    );
    $stmt->bind_param("ii", $attempt_id, $user_id);
    $stmt->execute();
    $attempt = $stmt->get_result()->fetch_assoc();

    //if the attempt doesn't exist or doesn't belong to this student, go back to the list
    if (!$attempt) {
        header("Location: quiz-history.php");
        exit;
    }

    $ans_stmt = $conn->prepare(
        "SELECT qaa.chosen_option, qaa.correct_option, qaa.is_correct, qaa.used_ai, qaa.skipped,
                q.question_id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d
         FROM QuizAttemptAnswers qaa
         JOIN Questions q ON q.question_id = qaa.question_id
         WHERE qaa.attempt_id = ?
         ORDER BY q.question_id ASC"
    );
    $ans_stmt->bind_param("i", $attempt_id);
    $ans_stmt->execute();
    $answers = $ans_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

//always load the full attempt list so the student can navigate between views
$list_stmt = $conn->prepare(
    "SELECT qa.attempt_id, qa.score, qa.total_questions, qa.attempted_at,
            l.lesson_title, l.lesson_id
     FROM QuizAttempts qa
     JOIN Lesson l ON l.lesson_id = qa.lesson_id
     WHERE qa.user_id = ?
     ORDER BY qa.attempted_at DESC"
);
$list_stmt->bind_param("i", $user_id);
$list_stmt->execute();
$all_attempts = $list_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/header.php'); ?>
    <title>Quiz History</title>
    <link rel="stylesheet" href="practice.css"/>
    <link rel="stylesheet" href="quiz-history.css"/>
</head>
<body>
    <?php include('includes/nav.php'); ?>

    <main>

        <?php if ($attempt): ?>
        <!-- DETAIL VIEW: per-question breakdown for a single attempt -->

        <div class="history-page-header">
            <a href="quiz-history.php" class="btn-secondary back-btn">Back to History</a>
            <div>
                <h2><?= htmlspecialchars($attempt['lesson_title']) ?></h2>
                <p>Attempted on <?= date('F j, Y \a\t g:i A', strtotime($attempt['attempted_at'])) ?></p>
            </div>
        </div>

        <div class="results-hero">
            <h2>Attempt Results</h2>
            <div class="score-big"><?= $attempt['score'] ?> / <?= $attempt['total_questions'] ?></div>
            <div class="score-pct"><?= round(($attempt['score'] / max($attempt['total_questions'], 1)) * 100) ?>% correct</div>
        </div>

        <h3 class="review-section-title">Question Review</h3>

        <div class="review-list">
        <?php foreach ($answers as $i => $ans):
            $is_correct  = (bool)$ans['is_correct'];
            $was_skipped = (bool)($ans['skipped'] ?? false);
            $chosen      = $ans['chosen_option']  ?? '';
            $correct     = $ans['correct_option'];
            $used_ai     = (bool)$ans['used_ai'];
            $options     = [
                'A' => $ans['option_a'],
                'B' => $ans['option_b'],
                'C' => $ans['option_c'],
                'D' => $ans['option_d'],
            ];
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
                        <?php if ($used_ai): ?>
                            <span class="review-badge badge-ai">AI Help</span>
                        <?php endif; ?>
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
                            <?php if ($letter === strtoupper($chosen) && !$is_correct): ?> &mdash; Your answer<?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
        </div>

        <div class="btn-row">
            <a href="practice.php?lesson_id=<?= $attempt['lesson_id'] ?>&reset=1" class="btn-primary">Retry This Lesson</a>
            <a href="quiz-history.php" class="btn-secondary">Back to History</a>
        </div>

        <?php else: ?>
        <!-- LIST VIEW: all previous attempts -->

        <div class="history-page-header">
            <div>
                <h2>Quiz History</h2>
                <p>All of your previous practice attempts are shown below.</p>
            </div>
        </div>

        <?php if (empty($all_attempts)): ?>
            <div class="empty-state">
                <p>You have not completed any practice quizzes yet.</p>
                <a href="choose-grade.php" class="btn-primary">Start Practicing</a>
            </div>
        <?php else: ?>
            <div class="attempt-list">
            <?php foreach ($all_attempts as $row):
                $pct = round(($row['score'] / max($row['total_questions'], 1)) * 100);
                $pct_class = $pct >= 80 ? 'pct-high' : ($pct >= 50 ? 'pct-mid' : 'pct-low');
            ?>
                <div class="attempt-card">
                    <div class="attempt-card-left">
                        <div class="attempt-lesson-title"><?= htmlspecialchars($row['lesson_title']) ?></div>
                        <div class="attempt-date"><?= date('F j, Y \a\t g:i A', strtotime($row['attempted_at'])) ?></div>
                    </div>
                    <div class="attempt-card-right">
                        <div class="attempt-score-wrap">
                            <span class="attempt-score"><?= $row['score'] ?> / <?= $row['total_questions'] ?></span>
                            <span class="attempt-pct <?= $pct_class ?>"><?= $pct ?>%</span>
                        </div>
                        <div class="attempt-actions">
                            <a href="quiz-history.php?attempt_id=<?= $row['attempt_id'] ?>" class="btn-secondary btn-sm">View Details</a>
                            <a href="practice.php?lesson_id=<?= $row['lesson_id'] ?>&reset=1" class="btn-primary btn-sm">Retry</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; 2025 BrightStart Math Tutoring. All rights reserved.</p>
    </footer>

</body>
<!-- lines 1-199 written by Caleb McHaney -->
</html>
