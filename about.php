<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$page_title = "About Us";
include 'includes/header.php';
?>

    <section class="about-hero">
        <div class="container">
            <h1>About AutoElite</h1>
            <p>Your trusted partner in premium automotive experiences</p>
        </div>
    </section>

    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <h2>Our Story</h2>
                <p>Founded in 2010, AutoElite has grown from a small dealership to one of Kenya's most respected premium car providers. Our journey began with a simple vision: to provide exceptional vehicles coupled with unparalleled customer service.</p>
                <p>Over the years, we've built strong relationships with both customers and manufacturers, allowing us to offer an exclusive selection of vehicles that meet the highest standards of quality and performance.</p>

                <div class="mission-vision">
                    <div class="mission">
                        <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                        <p>To deliver exceptional automotive experiences by providing premium vehicles, personalized service, and comprehensive support throughout the ownership journey.</p>
                    </div>
                    <div class="vision">
                        <h3><i class="fas fa-eye"></i> Our Vision</h3>
                        <p>To be East Africa's most trusted and innovative premium car dealership, setting new standards in customer satisfaction and automotive excellence.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <h2>Meet Our Team</h2>
            <p class="subtitle">Dedicated professionals committed to your automotive needs</p>

            <div class="team-grid">
                <div class="team-member">
                    <img src="assets/images/team1.jpg" alt="John Doe">
                    <h3>John Doe</h3>
                    <p class="position">Founder & CEO</p>
                    <p>With over 20 years in the automotive industry, John leads our team with passion and expertise.</p>
                </div>

                <div class="team-member">
                    <img src="assets/images/team2.jpg" alt="Jane Smith">
                    <h3>Jane Smith</h3>
                    <p class="position">Sales Director</p>
                    <p>Jane's deep product knowledge ensures you find the perfect vehicle for your needs.</p>
                </div>

                <div class="team-member">
                    <img src="assets/images/team3.jpg" alt="Michael Johnson">
                    <h3>Michael Johnson</h3>
                    <p class="position">Finance Manager</p>
                    <p>Michael helps navigate financing options to make your dream car a reality.</p>
                </div>

                <div class="team-member">
                    <img src="assets/images/team4.jpg" alt="Sarah Williams">
                    <h3>Sarah Williams</h3>
                    <p class="position">Customer Care</p>
                    <p>Sarah ensures every interaction with AutoElite exceeds your expectations.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="values-section">
        <div class="container">
            <h2>Our Core Values</h2>

            <div class="values-grid">
                <div class="value-item">
                    <i class="fas fa-star"></i>
                    <h3>Excellence</h3>
                    <p>We strive for perfection in every aspect of our business, from vehicle selection to customer service.</p>
                </div>

                <div class="value-item">
                    <i class="fas fa-handshake"></i>
                    <h3>Integrity</h3>
                    <p>Honesty and transparency guide all our interactions and business decisions.</p>
                </div>

                <div class="value-item">
                    <i class="fas fa-users"></i>
                    <h3>Customer Focus</h3>
                    <p>Your satisfaction is our top priority, and we tailor our services to your unique needs.</p>
                </div>

                <div class="value-item">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Innovation</h3>
                    <p>We continuously adapt to bring you the latest automotive technologies and services.</p>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>