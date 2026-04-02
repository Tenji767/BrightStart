<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>
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
    echo "<option value='". htmlspecialchars($row['grade_id'])."'>".htmlspecialchars($row['grade_name'])."</option>";
}

?>

</select>

<div id="lessonBuilder"></div>

<button onclick="addText()">Add Text</button>
<button onclick="addImage()">Add Image</button>
<button onclick="addDiagram()">Add Diagram</button>

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
function saveLesson(){

const title = document.getElementById("lessonTitle").value;
const grade = document.getElementById("grade-select").value;

const formData = new FormData();

formData.append("title", title);
formData.append("grade", grade);

let lessonHTML = "";

// handle TEXT blocks
document.querySelectorAll(".blockContent").forEach(textarea => {

const text = textarea.value.trim();

if(text !== ""){
lessonHTML += `<p>${text}</p>`;
}

});

// handle IMAGE blocks
document.querySelectorAll(".blockImage").forEach((img, index)=>{

if(img.files[0]){

const filename = img.files[0].name;

formData.append("image"+index, img.files[0]);

lessonHTML += `<img src="uploads/${filename}" class="lesson-image">`;

}

});

// handle DIAGRAM blocks
document.querySelectorAll(".blockDiagram").forEach((diagram, index)=>{

if(diagram.files[0]){

const filename = diagram.files[0].name;

formData.append("diagram"+index, diagram.files[0]);

lessonHTML += `<img src="uploads/${filename}" class="lesson-diagram">`;

}

});

formData.append("html", lessonHTML);

fetch("saveLesson.php",{
method:"POST",
body:formData
})
.then(response => response.text())
.then(data => alert(data))
.catch(err => console.error(err));

}
</script>

</body>
</html>

<!--...I think most of this I learned from ChatGPT and a lot I just pasted over...this shouldn't count...-->