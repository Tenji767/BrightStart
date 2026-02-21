<?php
$removed = false;
$selected = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["remove"])) {

        if (!isset($_POST["selected_tutor"]) || $_POST["selected_tutor"] === "") {
            $errorMsg = "Please select a tutor first, then click - Remove Tutor.";
        } else {
            $removed = true;
            $selected = $_POST["selected_tutor"];
        }

    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tutor Management</title>
    </head>

    <h1>Tutor Management</h1>

        <br>

        <div class ="tutor-list">
            <a href="admin-tutor-create.php"><button>+ Add Tutor</button></a>
            <button type="submit" name="remove" form="removeForm">- Remove Tutor</button>
            </div>

            <!-- Used some of the same sorting startegy as the lesson management page, but with different options that would maybe fit the tutor page. -->

            <br>
            <select id="tutor-sort" onchange="sortTutorsByDate()">
                <option value="date-old">Date Added (Oldest)</option>
                <option value="date-new">Date Added (Newest)</option>
            </select>

            <br><br>

            <?php if ($errorMsg): ?>
                <p><strong><?php echo htmlspecialchars($errorMsg); ?></strong></p>
            <?php endif; ?>

            <?php if ($removed): ?>
                <p><strong>Tutor Removed: <?php echo htmlspecialchars($selected); ?></strong></p>
            <?php endif; ?>

            <!--Tutors Table and also the tutor type is for now just math tutors and not other subjects, that might need to be changed depending if other lessons are added that are not math.-->

            <form id="removeForm" action="tutor-manage.php" method="post">

            <table class="tutor-list">
                <TR>
                    <TD>Select</TD>
                    <TD>Tutors Name</TD>
                    <TD>Email</TD>
                    <TD>Username</TD>
                    <TD>Tutor Type: Math</TD>
                    <TD>Date Joined</TD>
                </TR>

            <?php if (!$removed || $selected !== "Noah"): ?>
            <TR class="tutor-row" data-date="2026-02-21">
                    <TD><input type="radio" name="selected_tutor" value="Noah"></TD>
                    <TD>Noah</TD>
                    <TD>noah@example.com</TD>
                    <TD>noah_rey</TD>
                    <TD>Math</TD>
                    <TD>2026-02-21</TD>
            <TR>
            <?php endif; ?>

            <?php if (!$removed || $selected !== "Test"): ?>
            <TR class="tutor-row" data-date="2026-02-22">
                    <TD><input type="radio" name="selected_tutor" value="Test"></TD>
                    <TD>Test</TD>
                    <TD>Bob@example.com</TD>
                    <TD>Bob_tutor</TD>
                    <TD>Math</TD>
                    <TD>2026-02-22</TD>
            <TR>
            <?php endif; ?>

            </TR>
            </table>

            </form>

        <BR>
        <H2>
            Add Tutor </H2>

            <p>Use the + Add Tutor button to go to the create tutor page.</p>

            <div class="manage-menu-select">
        <a href="admin-dashboard.php"><button>Back to Dashboard</button></a>
        </div>

<br>
<script>
function sortTutorsByDate() {
  const select = document.getElementById("tutor-sort");
  const mode = select.value;
  if (!mode) return;

  const table = document.querySelector("table.tutor-list");
  if (!table) return;

  const headerRow = table.querySelector("tr:not(.tutor-row)");
  const rows = Array.from(table.querySelectorAll("tr.tutor-row"));

  rows.sort((a, b) => {
    const da = Date.parse(a.dataset.date);
    const db = Date.parse(b.dataset.date);

    if (mode === "date-old") return da - db;
    if (mode === "date-new") return db - da;
    return 0;
  });

  table.innerHTML = "";
  table.appendChild(headerRow);
  rows.forEach(r => table.appendChild(r));
}
</script>

<!-- by Noah Reynolds-->