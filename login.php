<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header("Location: profile.php");
    exit();
}

$page_title = "Login";
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (loginUser($username, $password)) {
        header("Location: profile.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

    <section class="form-section">
        <div class="form-container">
            <h2>Login to Your Account</h2>
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="styled-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">Login</button>
            </form>

            <div class="form-footer">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                <p><a href="#">Forgot your password?</a></p>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>