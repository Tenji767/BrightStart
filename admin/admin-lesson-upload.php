<?php


?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Lesson Creation</title>
</head>
<body>

    <h1>Admin Lesson Creation</h1>

        <div class="admin-menu">
            <a href="admin-dashboard.php"><button type="button" class="admin-menu-item">Back to Dashboard</button></a>
            <!-- Your existing upload + description (kept, just organized) -->
        <h3>Upload Lesson File</h3>

        <label for="lesson-file"><strong>Upload Lesson File (PDF, DOCX, PPTX):</strong></label><br>
        <input type="file" id="lesson-file" name="lesson-file" accept=".pdf,.docx,.pptx"...>

        <br><br>

        <label for="lesson-description"><strong>Lesson Description (Summary):</strong></label><br>
        <textarea id="lesson-description" name="lesson-description" rows="4" cols="50" required
                  placeholder="Short summary for the admin/teacher list view..."></textarea>

        <br><br>

        <input type="submit" value="Upload Lesson">

        <br><br>

        <!-- Your preferred navigation button style -->
        <div class="admin-menu">
            <a href="admin-dashboard.php">
                <button type="button" class="admin-menu-item">Back to Dashboard page</button>
            </a>

            <a href="lesson-create.php">
                <button type="button" class="admin-menu-item">Back to lesson upload/create page</button>
            </a>
        </div>

    </form>

</body>
</html>
