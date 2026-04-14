<?php
error_reporting(E_ALL);//display errors upon starting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$editing = false;
$lesson_id = null;
$title = '';
$grade = '';
$html = '';
$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
if (isset($_GET['lesson_id'])) {
    $editing = true;
    $lesson_id = intval($_GET['lesson_id']);
    $stmt = $conn->prepare("SELECT lesson_title, grade_id, lesson_content_html FROM Lesson WHERE lesson_id = ?");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("i", $lesson_id);
    if (!$stmt->execute()) die("Execute failed: " . $stmt->error);
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $title = $row['lesson_title'];
        $grade = $row['grade_id'];
        $html = $row['lesson_content_html'];
    } else {
        die("Lesson not found");
    }
}
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
<h1 class="pagename"><?php echo $editing ? 'Edit Lesson' : 'Create Lesson'; ?></h1>
</div>


<div class="returnBox">
<a href="tutor-dashboard(notAI).php" class="returnBtn">To Dashboard</a>


</div>

<!-- text box to insert name of lesson -->
<input id="lessonTitle" placeholder="Lesson Title" value="<?php echo htmlspecialchars($title); ?>">
<br>
<!-- dropdown to select grade -->
<label for="grade-select">Select a grade</label>
<select id="grade-select">

<?php
// connect to database and pull all grades
$result = $conn->query("SELECT * FROM Grade");

if(!$result){
    die("Query failed: ".$conn->error);
}
//loop through grades as option values for the select statement
while($row = $result->fetch_assoc()){
    $selected = ($row['grade_id'] == $grade) ? ' selected' : '';
    echo "<option value='". htmlspecialchars($row['grade_id'])."'$selected>".htmlspecialchars($row['grade_name'])."</option>";
}

?>

</select>

<!-- lesson builder buttons that will call respective functions  -->
<div id="lessonBuilder">
<button onclick="addText()">Add Text</button>
<button onclick="addImage()">Add Image</button>
<button onclick="addDiagram()">Add Diagram</button>
<!-- save lessson button -->
<button type="button" onclick="saveLesson()"><?php echo $editing ? 'Update Lesson' : 'Save Lesson'; ?></button>
</div>
<?php if ($editing): ?>
<?php
$dom = new DOMDocument();
@$dom->loadHTML($html);
$blocks = [];
foreach ($dom->getElementsByTagName('p') as $p) {
    $blocks[] = ['type' => 'text', 'content' => $p->textContent];
}
foreach ($dom->getElementsByTagName('img') as $img) {
    $src = $img->getAttribute('src');
    $class = $img->getAttribute('class');
    if ($class == 'lesson-image') {
        $blocks[] = ['type' => 'image', 'src' => $src];
    } elseif ($class == 'lesson-diagram') {
        $blocks[] = ['type' => 'diagram', 'src' => $src];
    }
}
foreach ($blocks as $block) {
    if ($block['type'] == 'text') {
        echo '<div class="lesson-block"><h3>Text</h3><textarea class="blockContent">' . htmlspecialchars($block['content']) . '</textarea><button type="button" onclick="deleteBlock(this)" class="deleteBlockBtn">Delete Block</button></div>';
    } elseif ($block['type'] == 'image') {
        echo '<div class="lesson-block"><h3>Image</h3><input type="hidden" class="existingImage" value="' . htmlspecialchars($block['src']) . '"><input type="file" class="blockImage"><button type="button" onclick="deleteBlock(this)" class="deleteBlockBtn">Delete Block</button></div>';
    } elseif ($block['type'] == 'diagram') {
        echo '<div class="lesson-block"><h3>Diagram</h3><input type="hidden" class="existingDiagram" value="' . htmlspecialchars($block['src']) . '"><input type="file" class="blockDiagram"><button type="button" onclick="deleteBlock(this)" class="deleteBlockBtn">Delete Block</button></div>';
    }
}
?>
<?php endif; ?>
<!-- script for inserting blocks and saving lesson -->
<script>

// blocks will be added by appending to the lesson builder div and will have html inside them allowing for insertion
const builder = document.getElementById("lessonBuilder");
const editing = <?php echo $editing ? 'true' : 'false'; ?>;
const lessonId = <?php echo json_encode($lesson_id); ?>;

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
    if (editing) formData.append("lesson_id", lessonId);

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
        const existing = img.previousElementSibling;
        if (img.files[0]) {
            const filename = img.files[0].name;
            formData.append("image" + index, img.files[0]);
            lessonHTML += `<img src="admin/uploads/${filename}" class="lesson-image">`;
        } else if (existing && existing.classList.contains('existingImage')) {
            lessonHTML += `<img src="${existing.value}" class="lesson-image">`;
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