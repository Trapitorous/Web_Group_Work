<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$page_title = "Car Gallery";
include 'includes/header.php';

// Get all available cars
$cars = getCars(true);

// Check if a specific car was requested
$selected_car = null;
if (isset($_GET['car_id'])) {
    $selected_car = getCarById($_GET['car_id']);
}

// Handle test drive booking
$booking_success = false;
$booking_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_test_drive']) && isLoggedIn()) {
    $car_id = $_POST['car_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $notes = sanitize($_POST['notes']);

    if (bookTestDrive($_SESSION['user_id'], $car_id, $date, $time, $notes)) {
        $booking_success = true;
    } else {
        $booking_error = "Failed to book test drive. The car might not be available or you may have already booked it.";
    }
}
?>

    <section class="gallery-section">
        <h2>Our Vehicle Collection</h2>
        <p class="subtitle">Browse our premium selection of vehicles available for test drives</p>

        <?php if ($booking_success): ?>
            <div class="alert success">
                Test drive booked successfully! You can view your bookings in your <a href="profile.php">profile</a>.
            </div>
        <?php elseif (!empty($booking_error)): ?>
            <div class="alert error"><?php echo $booking_error; ?></div>
        <?php endif; ?>

        <div class="gallery-container">
            <?php foreach ($cars as $car): ?>
                <div class="gallery-item" data-car-id="<?php echo $car['id']; ?>">
                    <img src="<?php echo htmlspecialchars($car['image_path']); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                    <div class="car-overlay">
                        <h3><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h3>
                        <p><?php echo htmlspecialchars($car['year']); ?> • <?php echo htmlspecialchars($car['mileage']); ?> km</p>
                        <p class="price">KSh <?php echo number_format($car['price'], 2); ?></p>
                        <button class="btn view-details">View Details</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php if ($selected_car): ?>
    <div class="modal" id="car-details-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>

            <div class="car-details">
                <div class="car-images">
                    <img src="<?php echo htmlspecialchars($selected_car['image_path']); ?>" alt="<?php echo htmlspecialchars($selected_car['make'] . ' ' . $selected_car['model']); ?>">
                </div>

                <div class="car-info">
                    <h2><?php echo htmlspecialchars($selected_car['make'] . ' ' . $selected_car['model']); ?></h2>
                    <p class="year-mileage"><?php echo htmlspecialchars($selected_car['year']); ?> • <?php echo htmlspecialchars($selected_car['mileage']); ?> km</p>
                    <p class="price">KSh <?php echo number_format($selected_car['price'], 2); ?></p>

                    <div class="specs">
                        <div class="spec-item">
                            <i class="fas fa-gas-pump"></i>
                            <span><?php echo htmlspecialchars($selected_car['fuel_type']); ?></span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-cog"></i>
                            <span><?php echo htmlspecialchars($selected_car['transmission']); ?></span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-paint-brush"></i>
                            <span><?php echo htmlspecialchars($selected_car['color']); ?></span>
                        </div>
                    </div>

                    <div class="description">
                        <h3>Description</h3>
                        <p><?php echo htmlspecialchars($selected_car['description']); ?></p>
                    </div>

                    <?php if ($selected_car['available']): ?>
                        <div class="test-drive-form">
                            <h3>Book a Test Drive</h3>

                            <?php if (isLoggedIn()): ?>
                                <form action="gallery.php?car_id=<?php echo $selected_car['id']; ?>" method="POST">
                                    <input type="hidden" name="car_id" value="<?php echo $selected_car['id']; ?>">

                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="time">Time</label>
                                        <select id="time" name="time" required>
                                            <option value="">Select time</option>
                                            <option value="09:00:00">9:00 AM</option>
                                            <option value="10:00:00">10:00 AM</option>
                                            <option value="11:00:00">11:00 AM</option>
                                            <option value="12:00:00">12:00 PM</option>
                                            <option value="13:00:00">1:00 PM</option>
                                            <option value="14:00:00">2:00 PM</option>
                                            <option value="15:00:00">3:00 PM</option>
                                            <option value="16:00:00">4:00 PM</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">Additional Notes</label>
                                        <textarea id="notes" name="notes" rows="3"></textarea>
                                    </div>

                                    <button type="submit" name="book_test_drive" class="btn">Book Test Drive</button>
                                </form>
                            <?php else: ?>
                                <p>You need to <a href="login.php">login</a> to book a test drive.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert error">This vehicle is currently not available for test drives.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>