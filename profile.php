<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
requireLogin();

$page_title = "My Profile";
include 'includes/header.php';

$user = getUserDetails($_SESSION['user_id']);
$test_drives = getUserTestDrives($_SESSION['user_id']);

$update_success = false;
$profile_picture_updated = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Handle profile update
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }

        // Validate phone
        if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
            $errors['phone'] = "Invalid phone number format";
        }

        if (empty($errors)) {
            if (updateProfile($_SESSION['user_id'], $full_name, $email, $phone, $address)) {
                $update_success = true;
                $user = getUserDetails($_SESSION['user_id']); // Refresh user data
            }
        }
    } elseif (isset($_FILES['profile_picture'])) {
        // Handle profile picture upload
        $target_dir = "assets/images/uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            $errors['profile_picture'] = "File is not an image.";
        }

        // Check file size (2MB max)
        if ($_FILES["profile_picture"]["size"] > 2000000) {
            $errors['profile_picture'] = "Sorry, your file is too large (max 2MB).";
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['profile_picture'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        if (empty($errors)) {
            // Generate unique filename
            $new_filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $imageFileType;
            $target_path = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_path)) {
                if (updateProfilePicture($_SESSION['user_id'], $target_path)) {
                    $profile_picture_updated = true;
                }
            } else {
                $errors['profile_picture'] = "Sorry, there was an error uploading your file.";
            }
        }
    }
}
?>

    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-picture">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <div class="default-picture">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>

                    <form action="profile.php" method="POST" enctype="multipart/form-data" class="picture-form">
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                        <label for="profile_picture" class="btn">Change Photo</label>
                        <button type="submit" name="upload_picture" class="btn secondary">Upload</button>
                        <?php if (!empty($errors['profile_picture'])): ?>
                            <span class="error"><?php echo $errors['profile_picture']; ?></span>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone']); ?></p>
                </div>
            </div>

            <?php if ($update_success): ?>
                <div class="alert success">Profile updated successfully!</div>
            <?php endif; ?>

            <?php if ($profile_picture_updated): ?>
                <div class="alert success">Profile picture updated successfully!</div>
            <?php endif; ?>

            <div class="profile-tabs">
                <div class="tabs">
                    <button class="tab-btn active" data-tab="edit-profile">Edit Profile</button>
                    <button class="tab-btn" data-tab="test-drives">My Test Drives</button>
                </div>

                <div class="tab-content active" id="edit-profile">
                    <form action="profile.php" method="POST" class="styled-form">
                        <input type="hidden" name="update_profile" value="1">

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            <?php if (!empty($errors['email'])): ?>
                                <span class="error"><?php echo $errors['email']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                            <?php if (!empty($errors['phone'])): ?>
                                <span class="error"><?php echo $errors['phone']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>

                        <button type="submit" class="btn">Update Profile</button>
                    </form>
                </div>

                <div class="tab-content" id="test-drives">
                    <?php if (empty($test_drives)): ?>
                        <p>You haven't booked any test drives yet.</p>
                        <a href="gallery.php" class="btn">Browse Cars</a>
                    <?php else: ?>
                        <div class="bookings-list">
                            <?php foreach ($test_drives as $booking): ?>
                                <div class="booking-card">
                                    <div class="booking-car">
                                        <img src="<?php echo htmlspecialchars($booking['image_path']); ?>" alt="<?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?>">
                                        <div>
                                            <h4><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model'] . ' (' . $booking['year'] . ')'); ?></h4>
                                            <p>Status: <span class="status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></p>
                                        </div>
                                    </div>
                                    <div class="booking-details">
                                        <p><i class="fas fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($booking['scheduled_date'])); ?></p>
                                        <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($booking['scheduled_time'])); ?></p>
                                        <?php if (!empty($booking['notes'])): ?>
                                            <p><i class="fas fa-sticky-note"></i> <?php echo htmlspecialchars($booking['notes']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>