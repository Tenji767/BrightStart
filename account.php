<!-- 1-468 by Noah Reynolds -->
<!-- Account page functionality and integration with the database created by Nick DeBlock -->
<?php
session_start();
include_once "db_connect.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: login.php");
    exit();
}

// Default user data from session
$user = [
    'name' => $_SESSION['student_name'],
    'email' => $_SESSION['email'],
    'student_id' => $_SESSION['role'],
    'grade' => $_SESSION['grade'] ,
    'school' => $_SESSION['school'] ?? 'BrightStart School',
    'profile_picture' => $_SESSION['profile_picture'] ?? 'pfp.png'
];

$uploadMessage = '';
$uploadError = '';

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 2 * 1024 * 1024; // 2 MB

        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = mime_content_type($fileTmpPath);

        if (!in_array($fileType, $allowedTypes, true)) {
            $uploadError = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
        } elseif ($fileSize > $maxFileSize) {
            $uploadError = 'Profile picture must be 2 MB or smaller.';
        } else {
            $uploadDirectory = 'uploads/profile_pictures/';

            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $safeFileName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExtension;
            $destination = $uploadDirectory . $safeFileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $_SESSION['profile_picture'] = $destination;
                $user['profile_picture'] = $destination;
                $updateStmt = $conn->prepare("UPDATE StudentAccount SET profile_picture = ? WHERE student_id = ?");
                $updateStmt->bind_param("si", $destination, $_SESSION['user_id']);
                $updateStmt->execute();
                $uploadMessage = 'Profile picture updated successfully.';
            } else {
                $uploadError = 'There was a problem uploading the image.';
            }
        }
    } else {
        $uploadError = 'Please choose an image to upload.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Account</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #dfe2e6;
            color: #1f2f45;
        }

        .site-header {
            background: linear-gradient(90deg, #2f67df 0%, #1a89c8 100%);
            color: white;
            padding: 22px 36px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .brand-badge {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(180deg, #4d6fe6 0%, #4150b8 100%);
            border: 2px solid rgba(255, 255, 255, 0.35);
            box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.12);
        }

        .brand-title {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.22);
        }

        .brand-subtitle {
            margin: 4px 0 0 0;
            font-size: 14px;
            opacity: 0.95;
        }

        .header-links {
            display: flex;
            align-items: center;
            gap: 28px;
            font-size: 16px;
            font-weight: bold;
        }

        .header-links a {
            color: white;
            text-decoration: none;
        }

        .header-links a:hover {
            text-decoration: underline;
        }

        .page-container {
            max-width: 1350px;
            margin: 0 auto;
            padding: 42px 28px 50px;
        }

        .welcome-panel {
            background-color: #d9e5f0;
            border-left: 6px solid #2f67df;
            border-radius: 16px;
            padding: 34px 40px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
            text-align: center;
            margin-bottom: 34px;
        }

        .welcome-panel h1 {
            margin: 0 0 10px 0;
            font-size: 30px;
            color: #1d2e46;
        }

        .welcome-panel p {
            margin: 0;
            font-size: 16px;
            color: #5f7288;
        }

        .account-layout {
            display: grid;
            grid-template-columns: 330px 1fr;
            gap: 26px;
        }

        .card {
            background-color: #eef5fb;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
        }

        .profile-card {
            text-align: center;
            align-self: start;
        }

        .profile-card img {
            width: 170px;
            height: 170px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #2f67df;
            background-color: white;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.14);
        }

        .profile-card h2 {
            margin: 18px 0 8px;
            color: #203249;
            font-size: 26px;
        }

        .profile-meta {
            margin: 6px 0;
            color: #5c7189;
            font-size: 15px;
        }

        .section-title {
            margin: 0 0 18px 0;
            color: #1f2f45;
            font-size: 26px;
        }

        .section-subtitle {
            margin: -6px 0 20px 0;
            color: #667c93;
            font-size: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .info-box {
            background-color: white;
            border: 1px solid #d7e4ef;
            border-radius: 14px;
            padding: 16px;
        }

        .info-label {
            display: block;
            font-size: 12px;
            font-weight: bold;
            color: #6a7f95;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 17px;
            color: #213249;
            font-weight: 600;
            word-break: break-word;
        }

        .upload-section {
            margin-top: 22px;
            text-align: left;
            background-color: white;
            border-radius: 14px;
            padding: 16px;
            border: 1px solid #d7e4ef;
        }

        .upload-section label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #1f2f45;
        }

        .upload-section input[type="file"] {
            width: 100%;
            margin-bottom: 12px;
        }

        .upload-section button {
            width: 100%;
            background-color: #6f97c1;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.10);
        }

        .upload-section button:hover {
            background-color: #5f88b3;
        }

        .message {
            margin-top: 12px;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .actions {
            margin-top: 26px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .actions a {
            text-decoration: none;
            background-color: #6f97c1;
            color: white;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: bold;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.10);
        }

        .actions a:hover {
            background-color: #5f88b3;
        }

        .actions .secondary-link {
            background-color: #163763;
        }

        .actions .secondary-link:hover {
            background-color: #102c51;
        }

        .site-footer {
            margin-top: 40px;
            background-color: #07163d;
            color: white;
            text-align: center;
            padding: 28px 20px;
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .account-layout {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .header-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-links {
                gap: 18px;
                flex-wrap: wrap;
            }
        }
        /* style for the support popup */
        #supportPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            font-size: 15px;
            color: #333;
            white-space: nowrap;
            z-index: 1000;
            text-align: center;
            position: fixed; /* ensures centering works */
        }

        #supportPopup.visible {
            display: block;
        }

    </style>
   
</head>
<body>
    <script>
        function togglePopup() {
            var popup = document.getElementById('supportPopup');
            popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
        }
    </script>
    
    <header class="site-header">
        <div class="header-inner">
            <div class="brand-wrap">
                <div class="brand-badge"></div>
                <div>
                    <h1 class="brand-title">BrightStart Learning</h1>
                    <p class="brand-subtitle">Student Account Portal</p>
                </div>
            </div>

            <nav class="header-links">
                <a href="index.php">Home</a>
                <a href="helper2.php">Helper</a>
                <a href="account.php">Account</a>
            </nav>
        </div>
    </header>

    <div class="page-container">
        <section class="welcome-panel">
            <h1>Student Account</h1>
            <p>View your account details and manage your profile picture.</p>
        </section>

        <div class="account-layout">
            <div class="card profile-card">
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="profile-meta"><?php echo htmlspecialchars($user['school']); ?></p>
                <p class="profile-meta"><?php echo htmlspecialchars($user['grade']); ?></p>

                <div class="upload-section">
                    <form method="post" enctype="multipart/form-data">
                        <label for="profile_picture">Change Profile Picture</label>
                        <input type="file" name="profile_picture" id="profile_picture" accept=".jpg,.jpeg,.png,.gif,.webp">
                        <button type="submit" name="update_profile_picture">Upload New Picture</button>
                    </form>

                    <?php if ($uploadMessage !== ''): ?>
                        <div class="message success"><?php echo htmlspecialchars($uploadMessage); ?></div>
                    <?php endif; ?>

                    <?php if ($uploadError !== ''): ?>
                        <div class="message error"><?php echo htmlspecialchars($uploadError); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h2 class="section-title">Account Information</h2>
                <p class="section-subtitle">Your current student account details are shown below.</p>

                <div class="info-grid">
                    <div class="info-box">
                        <span class="info-label">Full Name</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Email Address</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Account Type</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['student_id']); ?></span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Grade Level</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['grade']); ?></span>
                    </div>

                                       
                </div>

                <div class="actions">
                    <!-- <a href="edit-account.php">Edit Account</a> -->
                    <a href="password_reset.php" class="secondary-link">Change Password</a>
                    <a href="quiz-history.php" class="secondary-link">Quiz History</a>
                    <a href="lesson-history.php" class="secondary-link">Lesson History</a>
                    <a action="logout.php" method="POST" href="logout.php" class="secondary-link">Logout</a>
                    <a id="openBtn" class="secondary-link" onclick="event.stopPropagation(); document.getElementById('supportPopup').style.display = document.getElementById('supportPopup').style.display === 'block' ? 'none' : 'block';">Help</a>
                </div>

                <div id="supportPopup">
                    <button onclick="document.getElementById('supportPopup').style.display='none'" style="position:absolute; top:10px; right:12px; background:none; border:none; font-size:18px; cursor:pointer; color:#888; line-height:1;">✕</button>
                    <p style="margin: 0 0 8px 0; font-weight: bold; font-size: 17px; color: #1f2f45;">Need Help?</p>
                    <p style="margin: 0; color: #5c7189;">For help, contact us at:</p>
                    <p style="margin: 10px 0 16px 0; font-weight: bold; color: #2f67df;">support@brightstart.space</p>
                    <button id="copyBtn" onclick="navigator.clipboard.writeText('support@brightstart.space').then(() => { document.getElementById('copyBtn').textContent = '✓ Copied!'; setTimeout(() => { document.getElementById('copyBtn').textContent = 'Copy Email'; }, 2000); })" style="background-color:#2f67df; color:white; border:none; border-radius:8px; padding:8px 18px; font-size:14px; font-weight:bold; cursor:pointer;">Copy Email</button>
                </div>


            </div>
        </div>
    </div>

    <footer class="site-footer">
        &copy; <?php echo date("Y"); ?> BrightStart Math Tutoring. All rights reserved.
    </footer>
</body>
</html>
<!-- lines 5, 54-56, 486-487 written by Caleb McHaney -->
<!-- lines 1-4, 6-53, 57-418, 420-483, 488-509 written by Nick DeBlock -->
<!-- lines 419, 484-485 written by Benjamin Nguyen -->
