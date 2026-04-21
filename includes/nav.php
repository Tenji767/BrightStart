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

<!-- styling needed for the hover logout button overtop the profile picture -->
<style>
.nav-profile-dropdown { position: relative; }
.nav-profile-menu {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 0.4rem);
    background: white;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    overflow: hidden;
}
.nav-profile-dropdown:hover .nav-profile-menu { display: block; }
.nav-profile-menu a {
    display: block;
    padding: 0.6rem 1.2rem;
    color: #1e293b;
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
    text-decoration: none;
}
.nav-profile-menu a:hover { background: #f1f5f9; }
</style>
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

            <li class="nav-profile-dropdown">
                <a href="account.php"><img src="<?php echo htmlspecialchars($navProfilePicture); ?>" alt="Profile Picture" /></a>
                <div class="nav-profile-menu">
                    <a href="logout.php">Log Out</a>
                </div>
            </li>
        </ul>
        
    </nav>
    
</header>
<!-- nav updated to include only two links (lines 7-9) by Benjamin Nguyen -->
