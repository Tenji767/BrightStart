
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Lesson Creation</title>
</head>

<body>
<h1>Create Lesson</h1>

<input id="lessonTitle" placeholder="Lesson Title">

<label for="grade-select">Select a grade</label>
<select id="grade-select" name="grades">

     <?php

$conn = new mysqli( "sql112.infinityfree.com", "if0_41201125", "EvKOulpa615P", "if0_41201125_brightstar_db");
// log in and check to see if the query worked
$result = $conn->query("SELECT * FROM Grade");
if (!$result) {
    die("Query failed: " . $conn->error);
}
// loops through the list of grades and the database and displays a link to the concepts related to that grade
while ($row = $result->fetch_assoc()) {
    echo "<option value='".$row['grade_id']."'>".$row['grade_name']."</option>";}

?>
</select>
<div id="lessonBuilder"></div>



<button onclick="addText()">Add Text</button>
<button onclick="addImage()">Add Image</button>
<button onclick="addDiagram()">Add Diagram</button>

<button onclick="saveLesson()">Save Lesson</button>

<script>
    const builder = document.getElementById("lessonBuilder");

function addText(){

const block = document.createElement("div");

block.innerHTML = `
<h3>Text</h3>
<textarea class="blockContent"></textarea>
`;

builder.appendChild(block);

}

function addImage(){

const block = document.createElement("div");

block.innerHTML = `
<h3>Image</h3>
<input type="file" class="blockImage">
`;

builder.appendChild(block);

}

function addDiagram(){

const block = document.createElement("div");

block.innerHTML = `
<h3>Diagram</h3>
<input type="file" class="blockDiagram">
`;

builder.appendChild(block);

}
//saving lesson
function saveLesson(){

const lessonTitle = document.getElementById("lessonTitle").value;

const gradeSelected = document.getElementById("grade-select").value;

const lessonHTML = document.getElementById("lessonBuilder").innerHTML;

fetch("saveLesson.php", {
    method: "POST",

    headers:{
        "Content-Type":"application.json"
    },

    body: JSON.stringify({
        title:lessonTitle,
        grade:gradeSelected,
        html:lessonHTML
    })
});
}
</script>
<?php



?>


</body>



</html>