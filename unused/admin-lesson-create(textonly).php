<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Lesson Creation</title>
</head>

<body>

<h1>Create Lesson</h1>
<p>Text only, stay tuned for image upgrades</p>

<input id="lessonTitle" placeholder="Lesson Title">

<label for="grade-select">Select a grade</label>

<select id="grade-select">

<?php


$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

  
}


$result = $conn->query("SELECT * FROM Grade");


if(!$result){
    die("Query failed: ".$conn->error);
}

while($row = $result->fetch_assoc()){
    echo "<option value='".htmlspecialchars($row['grade_id'])."'>"
        .htmlspecialchars($row['grade_name']).
    "</option>";
}
?>

</select>

<div id="lessonBuilder"></div>

<button type="button" onclick="addText()">Add Text</button>
<button type="button" onclick="saveLesson()">Save Lesson</button>

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

function saveLesson(){

    const title = document.getElementById("lessonTitle").value;
    const grade = document.getElementById("grade-select").value;

    const formData = new FormData();

    formData.append("title", title);
    formData.append("grade", grade);

    let lessonHTML = "";

    document.querySelectorAll(".blockContent").forEach(textarea => {

        const text = textarea.value.trim();

        if(text !== ""){
            lessonHTML += `<p>${text}</p>`;
        }

    });

    formData.append("html", lessonHTML);

    fetch("saveLesson(textonly).php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        alert(data);
    })
    .catch(err => console.error(err));

}

</script>

</body>
</html>