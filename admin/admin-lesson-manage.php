<?php

?>


<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title>Manage lessons</title>
</head>

<h1>Manage Lessons</h1>

<div class="manage-menu-select"><!--For selecting create lesson from scratch or upload text from pdf-->
    <!-- Link these buttons to their corresponding pages -->
    <button>Create Lesson</button>
    <button>Upload Lesson</button>
</div>
<br>

<!-- For sorting the lessons and which appears first -->

<select id="lesson-sort">
    <option value="">Sort By</option>
    <option value="grade-asc">Grade ASC</option>
    <option value="grade-desc">Grade DESC</option>
    <option value="date-old">Date Added (Oldest)</option>
    <option value="date-new">Date Added (Newest)</option>
</select>

<!-- The table of lessons -->
<table class="lesson-list">
    <tr>
        <td>Lesson Name</td>
        <td>Grade</td>
        <td>Date Added</td>
        <td>Description</td>
    </tr>
</table>



</html>
<!-- By Benjamin Nguyen -->