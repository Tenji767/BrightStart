<?php


?>
//This is a placeholder page for the "Create Lesson" tool. The form and layout are based on the sketch of the Admin UI Diagram.
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

    <form action="admin-lesson-file.php" method="post" enctype="multipart/form-data">

        <h2>Create a Lesson File</h2>
        <p><em>Simple prototype. Tools are placeholders for now.</em></p>

        <a href="admin-dashboard.php"><button type="button" class="admin-menu-item">Back to Dashboard</button></a>

        <!-- Sketch-style layout: big text area + tool buttons on the right -->
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

        <!-- Requirements from partner sketch -->
        <h3>Lesson Details (Required)</h3>

        <label for="grade"><strong>Select Grade:</strong></label><br>
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

