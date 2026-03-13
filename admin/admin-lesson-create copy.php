<?php


?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Lesson Creation</title>
</head>

<body>
    <h1>Admin Lesson File Create</h1>

    <?php if (!empty($message)): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form action="admin-lesson-create.php" method="post" enctype="multipart/form-data">

        <h2>Create a Lesson File</h2>
        <p><em>This is just a Simple prototype. Tools are just placeholders for now.</em></p>

        <!-- Add Lesson Context and added tools beside it on the right side that text,image, and video as placeholders.  -->
        <table>
            <tr>
                <td style="vertical-align: top; width: 80%;">
                    <label for="lesson-content"><strong>Lesson Content (Text)</strong></label><br>
                    <textarea id="lesson-content" name="lesson-content" rows="12" cols="70"
                        placeholder="Type lesson text here..."></textarea>
                </td>

                <td style="vertical-align: top; padding-left: 12px;">
                    <strong>Tools</strong><br><br>
                    <button type="button">Text</button><br><br>
                    <button type="button">Image</button><br><br>
                    <button type="button">Video</button><br><br>
                    <em>(placeholders)</em>
                </td>
            </tr>
        </table>

        <hr>

        <!-- Able to select grade from K-12 for Lesson that is being created. -->
        <h3>Lesson Details (Required)</h3>

        <label for="grade"><strong>Select Grade K-12:</strong></label><br>
        <select id="grade" name="grade" required>
            <option value="" selected disabled>Select grade</option>
            <option value="K">K</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>

<!-- Able to name your concept/lesson and add a description for it and add any prerequisites that are required to suceed at the lesson. -->

        <br><br>

        <label for="concept-name"><strong>Concept Name:</strong></label><br>
        <input type="text" id="concept-name" name="concept-name" size="60" required
               placeholder="Example: Solving Two-Step Equations">

        <br><br>

        <label for="concept-description"><strong>Concept Description:</strong></label><br>
        <textarea id="concept-description" name="concept-description" rows="4" cols="70" required
                  placeholder="Brief explanation of what the student will learn..."></textarea>

        <br><br>

        <label for="prerequisites"><strong>Prerequisites:</strong></label><br>
        <textarea id="prerequisites" name="prerequisites" rows="3" cols="70"
                  placeholder="Example: Basic addition, subtraction, understanding variables..."></textarea>

                  <br><br>

        <input type="submit" value="Create Lesson">

        <br><br>

        <hr>
        
        <!-- Simple Buttons that will lead allow for easy navigation back to previous pages -->
        <div class="admin-menu">
            <a href="admin-lesson-manage.php">
                <button type="button" class="admin-menu-item">Back to Lesson Management page</button>
            </a>    
            
            <a href="admin-dashboard.php">
                <button type="button" class="admin-menu-item">Back to Dashboard page</button>
            </a>
        <!-- by Noah Reynolds-->

