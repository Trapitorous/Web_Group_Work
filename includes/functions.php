<?php
require_once 'config.php';

// Sanitize input data
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

// Register new user
function registerUser($username, $email, $password, $full_name, $phone, $address) {
    global $conn;

    // Check if username or email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return false; // User already exists
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $phone, $address);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}

// Login user
function loginUser($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, username, password, is_admin, profile_picture FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
            return true;
        }
    }
    return false;
}

// Get user details
function getUserDetails($user_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// Update user profile
function updateProfile($user_id, $full_name, $email, $phone, $address) {
    global $conn;

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);

    return $stmt->execute();
}

// Update profile picture
function updateProfilePicture($user_id, $image_path) {
    global $conn;

    $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $stmt->bind_param("si", $image_path, $user_id);

    if ($stmt->execute()) {
        $_SESSION['profile_picture'] = $image_path;
        return true;
    }
    return false;
}

// Get all cars
function getCars($available_only = true) {
    global $conn;

    $query = "SELECT * FROM cars";
    if ($available_only) {
        $query .= " WHERE available = TRUE";
    }
    $query .= " ORDER BY created_at DESC";

    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get car by ID
function getCarById($car_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// Book test drive
function bookTestDrive($user_id, $car_id, $date, $time, $notes) {
    global $conn;

    // Check if the car is available
    $car = getCarById($car_id);
    if (!$car || !$car['available']) {
        return false;
    }

    // Check if user already has a booking for this car
    $stmt = $conn->prepare("SELECT id FROM test_drives WHERE user_id = ? AND car_id = ? AND status IN ('pending', 'confirmed')");
    $stmt->bind_param("ii", $user_id, $car_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return false; // Already has a booking
    }

    // Insert new booking
    $stmt = $conn->prepare("INSERT INTO test_drives (user_id, car_id, scheduled_date, scheduled_time, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $car_id, $date, $time, $notes);

    return $stmt->execute();
}

// Get user's test drives
function getUserTestDrives($user_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT td.*, c.make, c.model, c.year, c.image_path 
                          FROM test_drives td 
                          JOIN cars c ON td.car_id = c.id 
                          WHERE td.user_id = ? 
                          ORDER BY td.scheduled_date DESC, td.scheduled_time DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Add contact message
function addContactMessage($name, $email, $phone, $address, $message) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, address, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $message);

    return $stmt->execute();
}


function getAllUsers() {
    global $conn;
    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllTestDrives() {
    global $conn;
    $result = $conn->query("SELECT td.*, u.username, u.full_name, c.make, c.model 
                          FROM test_drives td 
                          JOIN users u ON td.user_id = u.id 
                          JOIN cars c ON td.car_id = c.id 
                          ORDER BY td.created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addCar($make, $model, $year, $price, $mileage, $fuel_type, $transmission, $color, $description, $image_path) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO cars (make, model, year, price, mileage, fuel_type, transmission, color, description, image_path) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiissssss", $make, $model, $year, $price, $mileage, $fuel_type, $transmission, $color, $description, $image_path);

    return $stmt->execute();
}

function updateCar($car_id, $make, $model, $year, $price, $mileage, $fuel_type, $transmission, $color, $description, $available) {
    global $conn;

    $stmt = $conn->prepare("UPDATE cars SET make = ?, model = ?, year = ?, price = ?, mileage = ?, fuel_type = ?, transmission = ?, color = ?, description = ?, available = ? 
                           WHERE id = ?");
    $stmt->bind_param("ssiisssssii", $make, $model, $year, $price, $mileage, $fuel_type, $transmission, $color, $description, $available, $car_id);

    return $stmt->execute();
}

function deleteCar($car_id) {
    global $conn;

    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);

    return $stmt->execute();
}

function updateTestDriveStatus($booking_id, $status) {
    global $conn;

    $stmt = $conn->prepare("UPDATE test_drives SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);

    return $stmt->execute();
}
?>