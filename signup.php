<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header("Location: profile.php");
    exit();
}

$page_title = "Sign Up";
include 'includes/header.php';

$errors = [];
$username = $email = $full_name = $phone = $address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);

    // Validate username
    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Username can only contain letters, numbers and underscores";
    } elseif (strlen($username) < 4) {
        $errors['username'] = "Username must be at least 4 characters";
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = "Password must contain at least one uppercase letter";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors['password'] = "Password must contain at least one lowercase letter";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Password must contain at least one number";
    } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors['password'] = "Password must contain at least one special character";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Validate full name
    if (empty($full_name)) {
        $errors['full_name'] = "Full name is required";
    }

    // Validate phone
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format";
    }

    // Validate address
    if (empty($address)) {
        $errors['address'] = "Address is required";
    }

    if (empty($errors)) {
        if (registerUser($username, $email, $password, $full_name, $phone, $address)) {
            echo "<script>alert('Registration successful! Please login.'); window.location.href='login.php';</script>";
            exit();
        } else {
            $errors['general'] = "Registration failed. Username or email may already exist.";
        }
    }
}
?>

    <section class="form-section">
        <div class="form-container">
            <h2>Create Your Account</h2>
            <?php if (!empty($errors['general'])): ?>
                <div class="alert error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>

            <form action="signup.php" method="POST" id="signup-form" class="styled-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <?php if (!empty($errors['username'])): ?>
                        <span class="error"><?php echo $errors['username']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                    <small>Must be at least 8 characters with uppercase, lowercase, number, and special character</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <span class="error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                    <?php if (!empty($errors['full_name'])): ?>
                        <span class="error"><?php echo $errors['full_name']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                    <?php if (!empty($errors['phone'])): ?>
                        <span class="error"><?php echo $errors['phone']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required><?php echo htmlspecialchars($address); ?></textarea>
                    <?php if (!empty($errors['address'])): ?>
                        <span class="error"><?php echo $errors['address']; ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn">Register</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>