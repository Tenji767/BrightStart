<?php


?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Lesson Upload</title>
</head>
<body>

    <h1>Admin Lesson Upload</h1>

        <div class="admin-menu">
            <!-- Upload option and a description for the Lesson -->
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

         <!-- Simple Buttons that will lead allow for easy navigation back to previous pages -->
        <div class="admin-menu">
            <a href="admin-lesson-manage.php">
                <button type="button" class="admin-menu-item">Back to Lesson Management page</button>
            </a>    
            
            <a href="admin-dashboard.php">
                <button type="button" class="admin-menu-item">Back to Dashboard page</button>
            </a>

        </div>

    </form>

</body>
</html>

<!-- by Noah Reynolds-->