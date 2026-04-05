<?php
error_reporting(E_ALL);//display errors upon starting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>
<!DOCTYPE HTML>
<!-- standard head -->
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Lesson Creation</title>
<link rel="stylesheet" href="admin-style.css">
</head>

<body>



<div class="admin-header">
<h1 class="pagename">Create Lesson</h1>
</div>


<div class="returnBox">
<a href="admin-dashboard(notAI).php" class="returnBtn">To Dashboard</a>


</div>

<!-- text box to insert name of lesson -->
<input id="lessonTitle" placeholder="Lesson Title">
<br>
<!-- dropdown to select grade -->
<label for="grade-select">Select a grade</label>
<select id="grade-select">

<?php
// connect to database and pull all grades
$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection


}
$result = $conn->query("SELECT * FROM Grade");

if(!$result){
    die("Query failed: ".$conn->error);
}
//loop through grades as option values for the select statement
while($row = $result->fetch_assoc()){
    echo "<option value='". htmlspecialchars($row['grade_id'])."'>".htmlspecialchars($row['grade_name'])."</option>";
}

?>

</select>

<!-- lesson builder buttons that will call respective functions  -->
<div id="lessonBuilder">
<button onclick="addText()">Add Text</button>
<button onclick="addImage()">Add Image</button>
<button onclick="addDiagram()">Add Diagram</button>
<!-- save lessson button -->
<button type="button" onclick="saveLesson()">Save Lesson</button>
</div>
<!-- script for inserting blocks and saving lesson -->
<script>

// blocks will be added by appending to the lesson builder div and will have html inside them allowing for insertion
const builder = document.getElementById("lessonBuilder");

function deleteBlock(button) {
    button.parentElement.remove();
}

function addText() {
    const block = document.createElement("div");
    block.className = "lesson-block";  // Add class for easy removal
    block.innerHTML = `
        <h3>Text</h3>
        <textarea class="blockContent"></textarea>
        <button type="button" onclick="deleteBlock(this)" class="deleteBlockBtn">Delete Block</button>
    `;
    builder.appendChild(block);
}

function addImage() {
    const block = document.createElement("div");
    block.className = "lesson-block";  // Add class for easy removal
    block.innerHTML = `
        <h3>Image</h3>
        <input type="file" class="blockImage">
        <button type="button" onclick="deleteBlock(this)" class="deleteBlockBtn">Delete Block</button>
    `;
    builder.appendChild(block);
}

function addDiagram() {
    const block = document.createElement("div");
    block.className = "lesson-block";  // Add class for easy removal
    block.innerHTML = `
        <h3>Diagram</h3>
        <input type="file" class="blockDiagram">
        <button type="button" onclick="deleteBlock(this)" class="deleteBlockBtn">Delete Block</button>
    `;
    builder.appendChild(block);
}

// the lesson will be saved by gathering the content from the blocks and turning it into html and will be saved via the savelesson php
function saveLesson() {
    const title = document.getElementById("lessonTitle").value;
    const grade = document.getElementById("grade-select").value;

    const formData = new FormData();
    formData.append("title", title);
    formData.append("grade", grade);

    let lessonHTML = "";

    // Handle TEXT blocks
    document.querySelectorAll(".blockContent").forEach(textarea => {
        const text = textarea.value.trim();
        if (text !== "") {
            lessonHTML += `<p>${text}</p>`;
        }
    });

    // Handle IMAGE blocks
    document.querySelectorAll(".blockImage").forEach((img, index) => {
        if (img.files[0]) {
            const filename = img.files[0].name;
            formData.append("image" + index, img.files[0]);
            lessonHTML += `<img src="admin/uploads/${filename}" class="lesson-image">`;
        }
    });

    // Handle DIAGRAM blocks
    document.querySelectorAll(".blockDiagram").forEach((diagram, index) => {
        if (diagram.files[0]) {
            const filename = diagram.files[0].name;
            formData.append("diagram" + index, diagram.files[0]);
            lessonHTML += `<img src="admin/uploads/${filename}" class="lesson-diagram">`;
        }
    });

    formData.append("html", lessonHTML);

    fetch("saveLesson.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);  // Show the response message
        // Reset the form only if save was successful (assuming success message contains "success" or similar)
        if (data.toLowerCase().includes("success")) {
            // Clear the lesson title
            document.getElementById("lessonTitle").value = "";
            // Reset the grade select to the first option
            document.getElementById("grade-select").selectedIndex = 0;
            // Remove all lesson blocks (preserves the static buttons)
            document.querySelectorAll('.lesson-block').forEach(block => block.remove());
        }
    })
    .catch(err => console.error(err));
}

</script>

</body>
</html>

<!--...I think most of this I learned from ChatGPT and a lot I just pasted over...this shouldn't count...-->

<!-- Lines 1-168 by Benjamin Nguyen -->