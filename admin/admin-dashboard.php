<?php
session_start();

date_default_timezone_set('America/New_York');

/*
|--------------------------------------------------------------------------
| BrightStart Admin Dashboard
| Backend + Frontend in one file
|--------------------------------------------------------------------------
*/

$systemName = "BrightStart";
$pageTitle = "Admin Dashboard";
$adminName = isset($_SESSION['username']) && $_SESSION['username'] !== ''
    ? $_SESSION['username']
    : 'Admin';

$schools = [
    "BrightStart Elementary School",
    "BrightStart Middle School",
    "BrightStart High School"
];

if (!isset($_SESSION['selected_school'])) {
    $_SESSION['selected_school'] = $schools[0];
}

$statusMessage = '';
$statusType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['set_school'])) {
        $chosenSchool = isset($_POST['selected_school']) ? trim($_POST['selected_school']) : '';

        if (in_array($chosenSchool, $schools, true)) {
            $_SESSION['selected_school'] = $chosenSchool;
            $statusMessage = "Active school changed to " . $chosenSchool . ".";
            $statusType = 'success';
        } else {
            $statusMessage = "Please choose a valid school.";
            $statusType = 'error';
        }
    }

    if (isset($_POST['go_create_lesson'])) {
        $grade = isset($_POST['grade_level']) ? trim($_POST['grade_level']) : '';
        header("Location: admin-lesson-create(textonly).php?grade=" . urlencode($grade));//only uses text for now, switch to normal when images work
        exit();
    }

    if (isset($_POST['go_add_questions'])) {
        $grade = isset($_POST['grade_level']) ? trim($_POST['grade_level']) : '';
        header("Location: admin-create-questions.php?grade=" . urlencode($grade));
        exit();
    }
}

$currentSchool = $_SESSION['selected_school'];

$schoolAccessCount = 1;
$totalLessons = 12;
$totalPdfLessons = 9;
$totalQuestions = 37;

$recentActivity = [
    "Lesson content was updated for Grade 3 reading.",
    "A PDF lesson file was uploaded for Grade 5 science.",
    "New questions were added to a lesson.",
];

$currentDate = date("F j, Y");
$currentTime = date("g:i:s A");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
        }

        .page-wrap {
            max-width: 1180px;
            margin: 35px auto;
            padding: 20px;
        }

        .hero {
            background: linear-gradient(135deg, #1d3557, #457b9d);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        }

        .hero h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }

        .hero p {
            margin: 6px 0;
            font-size: 16px;
        }

        .status-box {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .status-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .status-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .section-title {
            margin: 0 0 14px 0;
            font-size: 24px;
            color: #1d3557;
        }

        .stats-grid,
        .tools-grid,
        .two-col-grid {
            display: grid;
            gap: 18px;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-bottom: 28px;
        }

        .tools-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            margin-bottom: 28px;
        }

        .two-col-grid {
            grid-template-columns: 1fr 1fr;
            margin-bottom: 28px;
        }

        .card {
            background-color: white;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .stat-card h2 {
            margin: 0 0 10px 0;
            color: #1d3557;
            font-size: 20px;
            text-align: center;
        }

        .stat-number {
            text-align: center;
            font-size: 34px;
            font-weight: bold;
            color: #457b9d;
        }

        .tool-card {
            display: block;
            text-decoration: none;
            background-color: #1d3557;
            color: white;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.10);
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .tool-card:hover {
            background-color: #457b9d;
            transform: translateY(-4px);
        }

        .tool-card-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .tool-card-text {
            font-size: 14px;
            line-height: 1.5;
        }

        .label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #1d3557;
        }

        select,
        input[type="text"],
        button {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 15px;
            margin-bottom: 12px;
        }

        button {
            background-color: #1d3557;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #457b9d;
        }

        .secondary-button {
            background-color: #457b9d;
        }

        .secondary-button:hover {
            background-color: #355f7d;
        }

        .info-list,
        .activity-list {
            margin: 0;
            padding-left: 20px;
        }

        .info-list li,
        .activity-list li {
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .school-badge {
            display: inline-block;
            background-color: #eaf2ff;
            color: #1d3557;
            padding: 10px 14px;
            border-radius: 999px;
            font-weight: bold;
            margin-top: 6px;
        }

        .footer-note {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }

        @media (max-width: 900px) {
            .two-col-grid {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

    <div class="page-wrap">

        <div class="hero">
            <h1><?php echo htmlspecialchars($systemName); ?> Admin Dashboard</h1>
            <p><strong>Welcome:</strong> <?php echo htmlspecialchars($adminName); ?></p>
            <p><strong>Date:</strong> <span id="live-date"><?php echo htmlspecialchars($currentDate); ?></span> | <strong>Time:</strong> <span id="live-time"><?php echo htmlspecialchars($currentTime); ?></span> EST</p>
            <p>This dashboard is focused on K-12 school access, lesson management, PDF uploads, and lesson questions.</p>
        </div>

        <?php if ($statusMessage !== ''): ?>
            <div class="status-box <?php echo $statusType === 'success' ? 'status-success' : 'status-error'; ?>">
                <?php echo htmlspecialchars($statusMessage); ?>
            </div>
        <?php endif; ?>

        <h2 class="section-title">Overview</h2>
        <div class="stats-grid">
            <div class="card stat-card">
                <h2>Active School Access</h2>
                <div class="stat-number"><?php echo $schoolAccessCount; ?></div>
            </div>

            <div class="card stat-card">
                <h2>Total Lessons</h2>
                <div class="stat-number"><?php echo $totalLessons; ?></div>
            </div>

            <div class="card stat-card">
                <h2>Lesson PDFs</h2>
                <div class="stat-number"><?php echo $totalPdfLessons; ?></div>
            </div>

            <div class="card stat-card">
                <h2>Total Questions</h2>
                <div class="stat-number"><?php echo $totalQuestions; ?></div>
            </div>
        </div>

        <div class="two-col-grid">
            <div class="card">
                <h2 class="section-title">School Access</h2>
                <p>Select the school context the tutor/admin is working in.</p>
                <div class="school-badge">
                    Current School: <?php echo htmlspecialchars($currentSchool); ?>
                </div>

                <form method="post" action="">
                    <label class="label" for="selected_school">Choose School</label>
                    <select name="selected_school" id="selected_school">
                        <?php foreach ($schools as $school): ?>
                            <option value="<?php echo htmlspecialchars($school); ?>" <?php echo $school === $currentSchool ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($school); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" name="set_school">Set Active School</button>
                </form>
            </div>

            <div class="card">
                <h2 class="section-title">Quick Lesson Actions</h2>
                <p>Choose a grade, then go to the related page.</p>

                <form method="post" action="">
                    <label class="label" for="grade_level">Grade Level</label>
                    <select name="grade_level" id="grade_level">
                        <option value="Kindergarten">Kindergarten</option>
                        <option value="Grade 1">Grade 1</option>
                        <option value="Grade 2">Grade 2</option>
                        <option value="Grade 3">Grade 3</option>
                        <option value="Grade 4">Grade 4</option>
                        <option value="Grade 5">Grade 5</option>
                        <option value="Grade 6">Grade 6</option>
                        <option value="Grade 7">Grade 7</option>
                        <option value="Grade 8">Grade 8</option>
                        <option value="Grade 9">Grade 9</option>
                        <option value="Grade 10">Grade 10</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12</option>
                    </select>

                    <button type="submit" name="go_create_lesson">Create / Edit Lesson</button>
                    <button type="submit" name="go_add_questions" class="secondary-button">Add Questions to Lesson</button>
                </form>
            </div>
        </div>

        <h2 class="section-title">Admin Tools</h2>
        <div class="tools-grid">
            <a class="tool-card" href="admin-lesson-create.php">
                <div class="tool-card-title">Create Lesson by Grade</div>
                <div class="tool-card-text">Create or edit a lesson for a specific grade level.</div>
            </a>

            <a class="tool-card" href="admin-lesson-create.php">
                <div class="tool-card-title">Upload Lesson PDF</div>
                <div class="tool-card-text">Upload a PDF to serve as the content for a lesson.</div>
            </a>

            <a class="tool-card" href="admin-create-questions.php">
                <div class="tool-card-title">Add Lesson Questions</div>
                <div class="tool-card-text">Add question text and answer text for a lesson. The lesson should be assigned automatically on that page.</div>
            </a>
        </div>

        <div class="two-col-grid">
            <div class="card">
                <h2 class="section-title">Lesson and Question Workflow</h2>
                <ul class="info-list">
                    <li>Select the active school.</li>
                    <li>Create a lesson for a specific K-12 grade.</li>
                    <li>Upload a PDF as the content for that lesson.</li>
                    <li>Add questions and answers to the lesson.</li>
                    <li>The lesson should be automatically associated when adding questions on the question page.</li>
                </ul>
            </div>

            <div class="card">
                <h2 class="section-title">Recent Activity</h2>
                <ul class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                        <li><?php echo htmlspecialchars($activity); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>

    <script>
        function updateDashboardClock() {
            const now = new Date();

            const dateOptions = {
                timeZone: 'America/New_York',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            const timeOptions = {
                timeZone: 'America/New_York',
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };

            const liveDate = document.getElementById('live-date');
            const liveTime = document.getElementById('live-time');

            if (liveDate) {
                liveDate.textContent = now.toLocaleDateString('en-US', dateOptions);
            }

            if (liveTime) {
                liveTime.textContent = now.toLocaleTimeString('en-US', timeOptions);
            }
        }

        updateDashboardClock();
        setInterval(updateDashboardClock, 1000);
    </script>
</body>
</html>
