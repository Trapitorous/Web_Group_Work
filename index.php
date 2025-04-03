<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$page_title = "Home";
include 'includes/header.php';

// Get featured cars
$featured_cars = getCars(true);
$featured_cars = array_slice($featured_cars, 0, 3); // Get first 3 cars
?>

    <section class="hero">
        <div class="hero-content">
            <h1>Experience Luxury Driving</h1>
            <p>Discover our premium collection of vehicles and book your test drive today.</p>
            <a href="gallery.php" class="btn">Explore Cars</a>
        </div>
    </section>

    <section class="featured-cars">
        <h2>Featured Vehicles</h2>
        <div class="car-grid">
            <?php foreach ($featured_cars as $car): ?>
                <div class="car-card">
                    <img src="<?php echo htmlspecialchars($car['image_path']); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                    <div class="car-info">
                        <h3><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h3>
                        <p><?php echo htmlspecialchars($car['year']); ?> • <?php echo htmlspecialchars($car['mileage']); ?> km</p>
                        <p class="price">KSh <?php echo number_format($car['price'], 2); ?></p>
                        <a href="gallery.php?car_id=<?php echo $car['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="about-section">
        <div class="about-content">
            <h2>Why Choose AutoElite?</h2>
            <p>At AutoElite, we're committed to providing exceptional service and a seamless car buying experience. Our knowledgeable staff will guide you through every step of the process.</p>
            <ul>
                <li><i class="fas fa-check-circle"></i> Premium vehicle selection</li>
                <li><i class="fas fa-check-circle"></i> Competitive pricing</li>
                <li><i class="fas fa-check-circle"></i> Expert financing options</li>
                <li><i class="fas fa-check-circle"></i> Comprehensive after-sales service</li>
            </ul>
            <a href="about.php" class="btn">Learn More</a>
        </div>
        <div class="about-image">
            <img src="assets/images/showroom.jpg" alt="AutoElite Showroom">
        </div>
    </section>

<?php include 'includes/footer.php'; ?>