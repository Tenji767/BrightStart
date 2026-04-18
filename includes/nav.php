<!--nav.php include created by Jordan Munster --> 
<!-- Updated nav.php to include profile picture from the account.php by Noah Reynolds (Lines 3-8 and edited 21) -->
 <!-- removed porfile picture, unable to display at this time can be reinstated later (Nick DeBlock) -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$navProfilePicture = $_SESSION['profile_picture'] ?? 'pfp.png';
$navSchoolName = $_SESSION['school'] ?? '';
?>
<header>
    <a href="index.php"><img src="logo.png" alt="Brightstart logo"/></a>
    <div>
        <h1>BrightStart Learning</h1>
        <?php if ($navSchoolName): ?>
            <p class="nav-school-name"><?php echo htmlspecialchars($navSchoolName); ?></p>
        <?php endif; ?>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
  
            <li><a href="helper2.php">Helper</a></li>

            <li><a href="account.php"><img src="<?php echo htmlspecialchars($navProfilePicture); ?>" alt="Profile Picture" /></a></li>
        </ul>
        
    </nav>
    
</header>
<!-- nav updated to include only two links (lines 7-9) by Benjamin Nguyen -->
