<?php
error_reporting(E_ALL);//error reporting
ini_set('display_errors', 1);

$conn = new mysqli( "localhost", "brights1_adminuser", "agileninjascapstone2025", "brights1_dbprimary");//log in
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);//check forc connection

//db connect
}


$title = $_POST['title'] ?? '';//gets the stuff from the form (submitted from the create lesson)
$grade = $_POST['grade'] ?? '';
$html = $_POST['html'] ?? '';

$uploadDir = "uploads/";//pulls images from the uploads folder

// DEBUG FILES
echo "<pre>";
print_r($_FILES);
echo "</pre>";

foreach($_FILES as $file){//goes through each file and uploads it (files are images and diagrams)
    $path = $uploadDir . basename($file["name"]);

    if(move_uploaded_file($file["tmp_name"], $path)){//message based on file upload success
        echo "Uploaded: " . $file["name"] . "<br>";
    } else {
        echo "Failed: " . $file["name"] . "<br>";
    }
}

$stmt = $conn->prepare(//inserts the lesson title, grade, and html content into database
"INSERT INTO Lesson (lesson_title, grade_id, lesson_content_html)
VALUES (?, ?, ?)"
);

if(!$stmt){
    die("Prepare failed: " . $conn->error);
}//failure statement

$stmt->bind_param("sis", $title, $grade, $html);//binds the actual values to the statement

if(!$stmt->execute()){//runs statement execute and checks if it worked or not
    die("Execute failed: " . $stmt->error);
}

echo "Lesson saved";//confirmation
?>

<!-- lines 1-51 by Benjamin Nguyen -->