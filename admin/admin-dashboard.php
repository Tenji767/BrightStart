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


$currentDate = date("F j, Y");
$currentTime = date("g:i:s A");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #eef3f9;
            color: #243447;
        }

        .page-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background-color: #0f2747;
            color: white;
            padding: 16px 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.78);
            letter-spacing: 0.3px;
        }

        .topbar-meta {
            text-align: right;
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.92);
        }

        .hero {
            background: linear-gradient(135deg, #16345d, #3e6fa3);
            color: white;
            padding: 42px 40px 34px;
        }

        .hero h1 {
            margin: 0 0 12px 0;
            font-size: 38px;
            line-height: 1.2;
        }

        .hero p {
            margin: 6px 0;
            font-size: 17px;
            max-width: 900px;
        }

        .main-content {
            width: 100%;
            padding: 30px 40px 50px;
        }

        .status-box {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 22px;
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

        .section-block {
            margin-bottom: 30px;
        }

        .section-heading-row {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 16px;
            margin-bottom: 14px;
        }

        .section-title {
            margin: 0;
            font-size: 28px;
            color: #16345d;
        }

        .section-subtitle {
            margin: 4px 0 0 0;
            color: #607086;
            font-size: 14px;
        }        .stats-grid,
        .two-col-grid {
            display: grid;
            gap: 20px;
        }

        .stats-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .two-col-grid {
            grid-template-columns: 1fr 1fr;
        }

        .panel {
            background-color: white;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 28px rgba(17, 39, 69, 0.08);
            border: 1px solid rgba(22, 52, 93, 0.06);
        }

        .stat-card {
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-label {
            font-size: 15px;
            font-weight: bold;
            color: #4d5f75;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 40px;
            font-weight: bold;
            color: #214f83;
            margin-bottom: 8px;
        }

        .stat-note {
            color: #6b7a8c;
            font-size: 14px;
            line-height: 1.5;
        }

        .label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #16345d;
        }

        select,
        button {
            width: 100%;
            padding: 13px 14px;
            border-radius: 12px;
            border: 1px solid #cfd7e3;
            font-size: 15px;
            margin-bottom: 12px;
            background-color: white;
        }

        button {
            background-color: #16345d;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #214f83;
            transform: translateY(-1px);
        }

        .secondary-button {
            background-color: #3e6fa3;
        }

        .secondary-button:hover {
            background-color: #335f8d;
        }

        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #edf4ff;
            color: #16345d;
            padding: 12px 16px;
            border-radius: 999px;
            font-weight: bold;
            margin: 4px 0 16px;
        }

        .info-list,
        .activity-list {
            margin: 0;
            padding-left: 20px;
        }

        .info-list li,
        .activity-list li {
            margin-bottom: 12px;
            line-height: 1.6;
            color: #42556d;
        }

        .footer-note {
            margin-top: auto;
            background-color: #0f2747;
            color: rgba(255, 255, 255, 0.78);
            text-align: center;
            padding: 18px 24px;
            font-size: 14px;
        }

        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 850px) {
            .topbar,
            .hero,

            .topbar-meta {
                text-align: left;
            }

            .hero h1 {
                font-size: 30px;
            }

            .two-col-grid,
            .stats-grid {
                grid-template-columns: 1fr;
            }
}

    </style>
</head>
<body>
    <div class="page-shell">
        <header class="topbar">
            <div class="topbar-inner">
                <div>
                    <div class="brand-title"><?php echo htmlspecialchars($systemName); ?></div>
                    <div class="brand-subtitle">K-12 Lesson and Question Administration Portal</div>
                </div>
                <div class="topbar-meta">
                    <div><strong>Welcome:</strong> <?php echo htmlspecialchars($adminName); ?></div>
                    <div><strong>Date:</strong> <span id="live-date"><?php echo htmlspecialchars($currentDate); ?></span> | <strong>Time:</strong> <span id="live-time"><?php echo htmlspecialchars($currentTime); ?></span> ET</div>
                </div>
            </div>
        </header>

        <section class="hero">
            <h1>Admin Dashboard</h1>
            <p>Manage K-12 lesson creation, PDF lesson content, and question setup from one professional full-page control center.</p>
            <p>Use this dashboard to choose the active school, move into grade-level lesson work, and organize question content for each lesson.</p>
        </section>

        <main class="main-content">
            <?php if ($statusMessage !== ''): ?>
                <div class="status-box <?php echo $statusType === 'success' ? 'status-success' : 'status-error'; ?>">
                    <?php echo htmlspecialchars($statusMessage); ?>
                </div>
            <?php endif; ?>

            <section class="section-block">
                <div class="section-heading-row">
                    <div>
                        <h2 class="section-title">System Overview</h2>
                        <p class="section-subtitle">High-level view of school access, lesson totals, PDFs, and question content.</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="panel stat-card">
                        <div class="stat-label">Active School Access</div>
                        <div class="stat-number"><?php echo $schoolAccessCount; ?></div>
                        <div class="stat-note">Current dashboard context is set to one active school at a time.</div>
                    </div>

                    <div class="panel stat-card">
                        <div class="stat-label">Total Lessons</div>
                        <div class="stat-number"><?php echo $totalLessons; ?></div>
                        <div class="stat-note">Tracks all available lessons across the active K-12 environment.</div>
                    </div>

                    <div class="panel stat-card">
                        <div class="stat-label">Lesson PDFs</div>
                        <div class="stat-number"><?php echo $totalPdfLessons; ?></div>
                        <div class="stat-note">Shows how many lessons currently have PDF content uploaded.</div>
                    </div>

                    <div class="panel stat-card">
                        <div class="stat-label">Total Questions</div>
                        <div class="stat-number"><?php echo $totalQuestions; ?></div>
                        <div class="stat-note">Total question and answer entries prepared for lesson use.</div>
                    </div>
                </div>
            </section>

            <section class="section-block">
                <div class="section-heading-row">
                    <div>
                        <h2 class="section-title">School and Lesson Setup</h2>
                        <p class="section-subtitle">Choose the working school, then jump straight into grade-level lesson tasks.</p>
                    </div>
                </div>

                <div class="two-col-grid">
                    <div class="panel">
                        <h3 class="section-title" style="font-size: 22px;">School Access</h3>
                        <p class="section-subtitle" style="margin-bottom: 14px;">Select the school context the admin is working in.</p>

                        <div class="school-badge">Current School: <?php echo htmlspecialchars($currentSchool); ?></div>

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

                    <div class="panel">
                        <h3 class="section-title" style="font-size: 22px;">Quick Lesson Actions</h3>
                        <p class="section-subtitle" style="margin-bottom: 14px;">Choose a grade level and continue to the right admin page.</p>

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

                            <button type="submit" name="go_create_lesson">Create or Edit Lesson</button>
                            <button type="submit" name="go_add_questions" class="secondary-button">Add Questions to Lesson</button>
                        </form>
                    </div>
                </div>
            </section>

        </main>

        <footer class="footer-note">
            &copy; <?php echo date("Y"); ?> BrightStart Educational Systems. All rights reserved.   
        </footer>
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
