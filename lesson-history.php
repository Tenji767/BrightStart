<?php
// Author: Caleb McHaney
// lesson-history.php this shows a student all lessons they have previously accessed

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

$user_id = intval($_SESSION['user_id']);

$stmt = $conn->prepare(
    "SELECT lh.first_viewed_at, lh.last_viewed_at, lh.view_count,
            l.lesson_id, l.lesson_title, l.grade_id
     FROM LessonHistory lh
     JOIN Lesson l ON l.lesson_id = lh.lesson_id
     WHERE lh.student_id = ?
     ORDER BY lh.last_viewed_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/header.php'); ?>
    <title>Lesson History</title>
    <link rel="stylesheet" href="practice.css"/>
    <link rel="stylesheet" href="quiz-history.css"/>
</head>
<body>
    <?php include('includes/nav.php'); ?>

    <main>

        <div class="history-page-header">
            <a href="account.php" class="btn-secondary back-btn">Back to Account</a>
            <div>
                <h2>Lesson History</h2>
                <p>All of the lessons you have previously accessed are shown below.</p>
            </div>
        </div>

        <?php if (empty($history)): ?>
            <div class="empty-state">
                <p>You have not viewed any lessons yet.</p>
                <a href="choose-grade.php" class="btn-primary">Browse Lessons</a>
            </div>
        <?php else: ?>
            <div class="attempt-list">
            <?php foreach ($history as $row): ?>
                <div class="attempt-card">
                    <div class="attempt-card-left">
                        <div class="attempt-lesson-title"><?= htmlspecialchars($row['lesson_title']) ?></div>
                        <div class="attempt-date">
                            Last viewed: <?= date('F j, Y \a\t g:i A', strtotime($row['last_viewed_at'])) ?>
                        </div>
                        <div class="attempt-date">
                            First viewed: <?= date('F j, Y \a\t g:i A', strtotime($row['first_viewed_at'])) ?>
                        </div>
                    </div>
                    <div class="attempt-card-right">
                        <div class="attempt-score-wrap">
                            <span class="attempt-score"><?= (int)$row['view_count'] ?> <?= $row['view_count'] == 1 ? 'view' : 'views' ?></span>
                        </div>
                        <div class="attempt-actions">
                            <a href="lesson.php?grade_id=<?= (int)$row['grade_id'] ?>&concept_id=<?= (int)$row['lesson_id'] ?>" class="btn-primary btn-sm">Review</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; <?= date("Y") ?> BrightStart Math Tutoring. All rights reserved.</p>
    </footer>

</body>
<!-- lines 1-92 written by Caleb McHaney -->
</html>