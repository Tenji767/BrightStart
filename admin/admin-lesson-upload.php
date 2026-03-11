<?php


?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lesson Upload</title>
</head>

<body>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Account Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <style>
        body{ font-family: Arial, Helvetica, sans-serif; margin: 24px; line-height: 1.35; }

        div.student-account-list{ display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px; }
        button{ padding: 10px 12px; cursor:pointer; }

        .msg{ padding: 10px 12px; border: 1px solid #ddd; background: #fafafa; border-radius: 8px; margin: 12px 0; }
        .msg.error{ border-color: #e7b3b3; background: #fff3f3; }
        .msg.ok{ border-color: #b7e3c2; background: #f3fff6; }

        .filters{
            display:grid;
            grid-template-columns: 1fr 220px 220px;
            gap:12px;
            align-items:end;
            margin: 10px 0 10px;
        }
        .filters label{ display:block; font-size: 13px; margin-bottom: 6px; }
        .filters input, .filters select{ width:100%; padding:10px; }

        .search-row{ display:flex; gap:10px; }
        .search-row input{ flex:1; }

        table.student-account-list{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        th, td{
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
            word-wrap: break-word;
        }
        th{ background:#f6f6f6; }

        th:nth-child(1), td:nth-child(1){ width: 70px; text-align:center; }
        th:nth-child(5), td:nth-child(5){ width: 70px; text-align:center; }
        th:nth-child(6), td:nth-child(6){ width: 90px; }
        th:nth-child(7), td:nth-child(7){ width: 120px; }
        th:nth-child(8), td:nth-child(8){ width: 220px; }

        .note{ font-size: 13px; color: #444; margin-top: 8px; }

        @media (max-width: 900px){
            .filters{ grid-template-columns: 1fr; }
            table.student-account-list{ table-layout:auto; }
        }
    </style>
</head>

<body>


    <h1>Upload Lesson</h1>

    <?php if (!empty($message)): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form action="admin-lesson-create.php" method="post" enctype="multipart/form-data">


        <h2>Create uploaded lesson</h2>

    <body> 
        <label for="lesson-file"><strong>Upload Lesson File:</strong></label><br>
        <input type="file" id="lesson-file" name="lesson-file" accept=".txt,.doc,.docx,.pdf" required><br><br>

        <input type="submit" value="Upload Lesson">

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

        </select><br><br>
    </form>

    
        

    </body>
    