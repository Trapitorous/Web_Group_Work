<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoElite - <?php echo $page_title ?? 'Premium Car Dealership'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="header">
    <div class="container">
        <div class="logo">
            <a href="index.php">Auto<span>Elite</span></a>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="gallery.php" <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'class="active"' : ''; ?>>Gallery</a></li>
                <li><a href="about.php" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>About Us</a></li>
                <li><a href="contact.php" <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'class="active"' : ''; ?>>Contact</a></li>

                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php" <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'class="active"' : ''; ?>><i class="fas fa-user"></i> Profile</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/dashboard.php" <?php echo strpos($_SERVER['PHP_SELF'], 'admin/') !== false ? 'class="active"' : ''; ?>><i class="fas fa-cog"></i> Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active"' : ''; ?>><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="signup.php" <?php echo basename($_SERVER['PHP_SELF']) == 'signup.php' ? 'class="active"' : ''; ?>><i class="fas fa-user-plus"></i> Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<div class="mobile-menu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if (isLoggedIn()): ?>
            <li><a href="profile.php">Profile</a></li>
            <?php if (isAdmin()): ?>
                <li><a href="admin/dashboard.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="signup.php">Sign Up</a></li>
        <?php endif; ?>
    </ul>
</div>

<main class="container">