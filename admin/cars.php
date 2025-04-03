<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Manage Cars";
include 'header.php';

// Handle car actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $car_id = $_GET['id'] ?? 0;

    switch ($action) {
        case 'delete':
            // Get car info before deleting to remove image
            $car = getCarById($car_id);
            if ($car && deleteCar($car_id)) {
                // Delete associated image
                if (!empty($car['image_path']) && file_exists("../../" . $car['image_path'])) {
                    unlink("../../" . $car['image_path']);
                }
                $_SESSION['message'] = "Car deleted successfully";
            } else {
                $_SESSION['error'] = "Failed to delete car";
            }
            break;

        case 'toggle_availability':
            $car = getCarById($car_id);
            if ($car) {
                $available = $car['available'] ? 0 : 1;
                if (updateCar($car_id, $car['make'], $car['model'], $car['year'], $car['price'], $car['mileage'], $car['fuel_type'], $car['transmission'], $car['color'], $car['description'], $available)) {
                    $_SESSION['message'] = "Car availability updated";
                } else {
                    $_SESSION['error'] = "Failed to update car availability";
                }
            }
            break;
    }

    header("Location: cars.php");
    exit();
}

// Handle add/edit car form
$edit_mode = false;
$car_data = [
    'id' => 0,
    'make' => '',
    'model' => '',
    'year' => date('Y'),
    'price' => '',
    'mileage' => '',
    'fuel_type' => 'Petrol',
    'transmission' => 'Automatic',
    'color' => '',
    'description' => '',
    'available' => 1,
    'image_path' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id'] ?? 0;
    $make = sanitize($_POST['make']);
    $model = sanitize($_POST['model']);
    $year = (int)$_POST['year'];
    $price = (float)$_POST['price'];
    $mileage = (int)$_POST['mileage'];
    $fuel_type = sanitize($_POST['fuel_type']);
    $transmission = sanitize($_POST['transmission']);
    $color = sanitize($_POST['color']);
    $description = sanitize($_POST['description']);
    $available = isset($_POST['available']) ? 1 : 0;
    $current_image = $_POST['current_image'] ?? '';

    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../../assets/images/cars/";

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Validate image file
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = "File is not an image.";
            header("Location: cars.php");
            exit();
        }

        // Check file size (max 2MB)
        if ($_FILES["image"]["size"] > 2000000) {
            $_SESSION['error'] = "Sorry, your file is too large (max 2MB).";
            header("Location: cars.php");
            exit();
        }

        // Allow certain file formats
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: cars.php");
            exit();
        }

        // Generate unique filename
        $new_filename = 'car_' . uniqid() . '.' . $file_extension;
        $target_path = $target_dir . $new_filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
            $image_path = "assets/images/cars/" . $new_filename;

            // Delete old image if editing and new image uploaded successfully
            if ($car_id > 0 && !empty($current_image) && file_exists("../../" . $current_image)) {
                unlink("../../" . $current_image);
            }
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header("Location: cars.php");
            exit();
        }
    } elseif ($car_id > 0) {
        // Keep existing image if no new image uploaded during edit
        $image_path = $current_image;
    } elseif ($car_id == 0) {
        // Require image for new cars
        $_SESSION['error'] = "Car image is required.";
        header("Location: cars.php");
        exit();
    }

    if ($car_id > 0) {
        // Edit existing car
        if (updateCar($car_id, $make, $model, $year, $price, $mileage, $fuel_type, $transmission, $color, $description, $available, $image_path)) {
            $_SESSION['message'] = "Car updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update car";
        }
    } else {
        // Add new car
        if (addCar($make, $model, $year, $price, $mileage, $fuel_type, $transmission, $color, $description, $image_path)) {
            $_SESSION['message'] = "Car added successfully";
        } else {
            $_SESSION['error'] = "Failed to add car";
        }
    }

    header("Location: cars.php");
    exit();
}

// Check if editing a car
if (isset($_GET['edit'])) {
    $car_id = $_GET['edit'];
    $car = getCarById($car_id);
    if ($car) {
        $edit_mode = true;
        $car_data = $car;
    }
}

// Get all cars
$cars = getCars(false);
?>

    <div class="admin-content">
        <div class="admin-header">
            <h2>Manage Vehicles</h2>
            <button id="add-car-btn" class="btn">Add New Vehicle</button>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="car-form-container <?php echo $edit_mode ? 'active' : ''; ?>" id="car-form-container">
            <h3><?php echo $edit_mode ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h3>

            <form action="cars.php" method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="car_id" value="<?php echo $car_data['id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo $car_data['image_path'] ?? ''; ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="make">Make</label>
                        <input type="text" id="make" name="make" value="<?php echo htmlspecialchars($car_data['make']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="model">Model</label>
                        <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($car_data['model']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="number" id="year" name="year" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo $car_data['year']; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (KSh)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" value="<?php echo $car_data['price']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="mileage">Mileage (km)</label>
                        <input type="number" id="mileage" name="mileage" min="0" value="<?php echo $car_data['mileage']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="fuel_type">Fuel Type</label>
                        <select id="fuel_type" name="fuel_type" required>
                            <option value="Petrol" <?php echo $car_data['fuel_type'] == 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                            <option value="Diesel" <?php echo $car_data['fuel_type'] == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                            <option value="Hybrid" <?php echo $car_data['fuel_type'] == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                            <option value="Electric" <?php echo $car_data['fuel_type'] == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="transmission">Transmission</label>
                        <select id="transmission" name="transmission" required>
                            <option value="Automatic" <?php echo $car_data['transmission'] == 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                            <option value="Manual" <?php echo $car_data['transmission'] == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="text" id="color" name="color" value="<?php echo htmlspecialchars($car_data['color']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="available">Available</label>
                        <div class="checkbox-container">
                            <input type="checkbox" id="available" name="available" <?php echo $car_data['available'] ? 'checked' : ''; ?>>
                            <label for="available" class="toggle"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($car_data['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Car Image</label>
                    <?php if ($edit_mode && !empty($car_data['image_path'])): ?>
                        <div class="current-image-container">
                            <img src="<?php echo '../../' . htmlspecialchars($car_data['image_path']); ?>"
                                 alt="Current Car Image"
                                 class="current-image">
                            <p class="current-image-text">Current image (upload new to replace)</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*" <?php echo !$edit_mode ? 'required' : ''; ?>>
                    <small>Allowed formats: JPG, JPEG, PNG, GIF (Max 2MB)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn"><?php echo $edit_mode ? 'Update Vehicle' : 'Add Vehicle'; ?></button>
                    <button type="button" id="cancel-edit" class="btn secondary">Cancel</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Make & Model</th>
                    <th>Year</th>
                    <th>Price</th>
                    <th>Mileage</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td>
                            <?php if (!empty($car['image_path'])): ?>
                                <img src="<?php echo '../../' . htmlspecialchars($car['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>"
                                     class="car-thumbnail">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></td>
                        <td><?php echo $car['year']; ?></td>
                        <td>KSh <?php echo number_format($car['price'], 2); ?></td>
                        <td><?php echo number_format($car['mileage']); ?> km</td>
                        <td>
                            <span class="status-badge status-<?php echo $car['available'] ? 'available' : 'unavailable'; ?>">
                                <?php echo $car['available'] ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="cars.php?edit=<?php echo $car['id']; ?>" class="btn small">Edit</a>
                            <a href="cars.php?action=toggle_availability&id=<?php echo $car['id']; ?>" class="btn small <?php echo $car['available'] ? 'warning' : 'success'; ?>">
                                <?php echo $car['available'] ? 'Make Unavailable' : 'Make Available'; ?>
                            </a>
                            <a href="cars.php?action=delete&id=<?php echo $car['id']; ?>" class="btn small danger" onclick="return confirm('Are you sure you want to delete this car?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addCarBtn = document.getElementById('add-car-btn');
            const carFormContainer = document.getElementById('car-form-container');
            const cancelEditBtn = document.getElementById('cancel-edit');

            if (addCarBtn && carFormContainer) {
                addCarBtn.addEventListener('click', function() {
                    carFormContainer.classList.add('active');
                    window.scrollTo({
                        top: carFormContainer.offsetTop - 20,
                        behavior: 'smooth'
                    });
                });
            }

            if (cancelEditBtn && carFormContainer) {
                cancelEditBtn.addEventListener('click', function() {
                    carFormContainer.classList.remove('active');
                    window.location.href = 'cars.php';
                });
            }
        });
    </script>

<?php require_once '../includes/footer.php';?>