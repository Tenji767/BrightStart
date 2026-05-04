<?php
// Author: Caleb McHaney
session_start();
$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'teacher' && $role !== 'admin')) {
    header("Location: ../login.php");
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_GET['lesson_id'])) {
    header("Location: tutor-manage-lessons.php");
    exit();
}

$lesson_id = intval($_GET['lesson_id']);
$stmt = $conn->prepare("SELECT lesson_title, grade_id, lesson_content_html FROM Lesson WHERE lesson_id = ?");
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    die("Lesson not found.");
}

$title = $row['lesson_title'];
$grade  = $row['grade_id'];
$html_content = $row['lesson_content_html'];

// Parse stored HTML preserving block order
$blocks = [];
if (!empty($html_content)) {
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8"?>' . $html_content);
    $body = $dom->getElementsByTagName('body')->item(0);
    if ($body) {
        foreach ($body->childNodes as $node) {
            if (!($node instanceof DOMElement)) continue;
            if ($node->nodeName === 'p') {
                $blocks[] = ['type' => 'text', 'content' => $node->textContent];
            } elseif ($node->nodeName === 'img') {
                $class = $node->getAttribute('class');
                $src   = $node->getAttribute('src');
                if ($class === 'lesson-image') {
                    $blocks[] = ['type' => 'image', 'src' => $src];
                } elseif ($class === 'lesson-diagram') {
                    $blocks[] = ['type' => 'diagram', 'src' => $src];
                }
            }
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Lesson</title>
    <link rel="stylesheet" href="tutor-style.css">
    <style>
        .lesson-block {
            border: 1px solid #ccc;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            background: #fafafa;
        }
        .lesson-block h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .lesson-block textarea {
            width: 100%;
            min-height: 80px;
            box-sizing: border-box;
        }
        .block-preview {
            max-width: 200px;
            max-height: 150px;
            display: block;
            margin-bottom: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .deleteBlockBtn {
            margin-top: 8px;
            background: #c0392b;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .deleteBlockBtn:hover { background: #a93226; }
        #lessonBuilder { margin: 15px 0; display: flex; gap: 10px; flex-wrap: wrap; }
        #lessonBuilder button { padding: 8px 14px; }
        #blocks-container { margin-top: 10px; }
    </style>
</head>
<body>

<div class="admin-header">
    <h1 class="pagename">Edit Lesson</h1>
</div>

<div class="returnBox">
    <a href="tutor-manage-lessons.php" class="returnBtn">Back to Manage Lessons</a>
</div>

<input id="lessonTitle" placeholder="Lesson Title" value="<?php echo htmlspecialchars($title); ?>">
<br>

<label for="grade-select">Select a grade</label>
<select id="grade-select">
<?php
$grade_result = $conn->query("SELECT * FROM Grade");
if (!$grade_result) die("Query failed: " . $conn->error);
while ($grade_row = $grade_result->fetch_assoc()) {
    $selected = ($grade_row['grade_id'] == $grade) ? ' selected' : '';
    echo "<option value='" . htmlspecialchars($grade_row['grade_id']) . "'" . $selected . ">" . htmlspecialchars($grade_row['grade_name']) . "</option>";
}
?>
</select>

<div id="lessonBuilder">
    <button type="button" onclick="addText()">Add Text</button>
    <button type="button" onclick="addImage()">Add Image</button>
    <button type="button" onclick="addDiagram()">Add Diagram</button>
    <button type="button" onclick="saveLesson()">Update Lesson</button>
</div>

<div id="blocks-container">
<?php foreach ($blocks as $block): ?>
    <?php if ($block['type'] === 'text'): ?>
        <div class="lesson-block">
            <h3>Text</h3>
            <textarea class="blockContent"><?php echo htmlspecialchars($block['content']); ?></textarea>
            <button type="button" class="deleteBlockBtn" onclick="deleteBlock(this)">Delete Block</button>
        </div>
    <?php elseif ($block['type'] === 'image'): ?>
        <div class="lesson-block">
            <h3>Image</h3>
            <img src="../<?php echo htmlspecialchars($block['src']); ?>" class="block-preview" onerror="this.style.display='none'">
            <p style="font-size:12px;color:#888;">Replace with a new file, or leave blank to keep existing.</p>
            <input type="hidden" class="existingImage" value="<?php echo htmlspecialchars($block['src']); ?>">
            <input type="file" class="blockImage" accept="image/*">
            <button type="button" class="deleteBlockBtn" onclick="deleteBlock(this)">Delete Block</button>
        </div>
    <?php elseif ($block['type'] === 'diagram'): ?>
        <div class="lesson-block">
            <h3>Diagram</h3>
            <img src="../<?php echo htmlspecialchars($block['src']); ?>" class="block-preview" onerror="this.style.display='none'">
            <p style="font-size:12px;color:#888;">Replace with a new file, or leave blank to keep existing.</p>
            <input type="hidden" class="existingDiagram" value="<?php echo htmlspecialchars($block['src']); ?>">
            <input type="file" class="blockDiagram" accept="image/*">
            <button type="button" class="deleteBlockBtn" onclick="deleteBlock(this)">Delete Block</button>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
</div>

<script>
const lessonId = <?php echo json_encode($lesson_id); ?>;
const container = document.getElementById("blocks-container");

function deleteBlock(btn) {
    btn.closest(".lesson-block").remove();
}

function addText() {
    const block = document.createElement("div");
    block.className = "lesson-block";
    block.innerHTML = `
        <h3>Text</h3>
        <textarea class="blockContent"></textarea>
        <button type="button" class="deleteBlockBtn" onclick="deleteBlock(this)">Delete Block</button>
    `;
    container.appendChild(block);
}

function addImage() {
    const block = document.createElement("div");
    block.className = "lesson-block";
    block.innerHTML = `
        <h3>Image</h3>
        <input type="file" class="blockImage" accept="image/*">
        <button type="button" class="deleteBlockBtn" onclick="deleteBlock(this)">Delete Block</button>
    `;
    container.appendChild(block);
}

function addDiagram() {
    const block = document.createElement("div");
    block.className = "lesson-block";
    block.innerHTML = `
        <h3>Diagram</h3>
        <input type="file" class="blockDiagram" accept="image/*">
        <button type="button" class="deleteBlockBtn" onclick="deleteBlock(this)">Delete Block</button>
    `;
    container.appendChild(block);
}

function saveLesson() {
    const titleVal = document.getElementById("lessonTitle").value.trim();
    const gradeVal = document.getElementById("grade-select").value;

    if (!titleVal) {
        alert("Please enter a lesson title.");
        return;
    }

    const formData = new FormData();
    formData.append("title", titleVal);
    formData.append("grade", gradeVal);
    formData.append("lesson_id", lessonId);

    let lessonHTML = "";
    let imageIndex = 0;
    let diagramIndex = 0;

    // Walk blocks in DOM order so content order is preserved
    document.querySelectorAll("#blocks-container .lesson-block").forEach(block => {
        const textarea = block.querySelector(".blockContent");
        const imgInput  = block.querySelector(".blockImage");
        const diagInput = block.querySelector(".blockDiagram");

        if (textarea) {
            const text = textarea.value.trim();
            if (text) lessonHTML += `<p>${text}</p>`;
        } else if (imgInput) {
            const existing = block.querySelector(".existingImage");
            if (imgInput.files[0]) {
                const filename = imgInput.files[0].name;
                formData.append("image" + imageIndex, imgInput.files[0]);
                lessonHTML += `<img src="tutor/uploads/${filename}" class="lesson-image">`;
            } else if (existing) {
                lessonHTML += `<img src="${existing.value}" class="lesson-image">`;
            }
            imageIndex++;
        } else if (diagInput) {
            const existing = block.querySelector(".existingDiagram");
            if (diagInput.files[0]) {
                const filename = diagInput.files[0].name;
                formData.append("diagram" + diagramIndex, diagInput.files[0]);
                lessonHTML += `<img src="tutor/uploads/${filename}" class="lesson-diagram">`;
            } else if (existing) {
                lessonHTML += `<img src="${existing.value}" class="lesson-diagram">`;
            }
            diagramIndex++;
        }
    });

    formData.append("html", lessonHTML);

    fetch("saveLesson.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.text())
    .then(msg => {
        alert(msg);
        if (msg.toLowerCase().includes("success")) {
            window.location.href = "tutor-manage-lessons.php";
        }
    })
    .catch(err => console.error(err));
}
</script>

</body>
</html>
<!-- lines 1-280 written by Caleb McHaney -->
