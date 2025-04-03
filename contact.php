<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$page_title = "Contact Us";
include 'includes/header.php';

$message_sent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $message = sanitize($_POST['message']);

    // Validation
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($phone)) {
        $errors['phone'] = "Phone number is required";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format";
    }

    if (empty($address)) {
        $errors['address'] = "Address is required";
    }

    if (empty($message)) {
        $errors['message'] = "Message is required";
    }

    if (empty($errors)) {
        if (addContactMessage($name, $email, $phone, $address, $message)) {
            $message_sent = true;
            // Clear form
            $name = $email = $phone = $address = $message = '';
        }
    }
}
?>

    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have questions about our vehicles or services? Reach out to us and our team will get back to you as soon as possible.</p>

                <div class="contact-details">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Address</h3>
                        <p>123 Auto Elite Avenue<br>Nairobi, Kenya</p>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <h3>Phone</h3>
                        <p>+254 712 345 678</p>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <h3>Email</h3>
                        <p>info@autoelite.com</p>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <h3>Working Hours</h3>
                        <p>Monday - Friday: 8:00 AM - 6:00 PM<br>
                            Saturday: 9:00 AM - 4:00 PM<br>
                            Sunday: Closed</p>
                    </div>
                </div>
            </div>

            <div class="contact-form-container">
                <?php if ($message_sent): ?>
                    <div class="alert success">
                        Thank you for your message! We'll get back to you soon.
                    </div>
                <?php else: ?>
                    <h2>Send Us a Message</h2>
                    <form action="contact.php" method="POST" class="styled-form">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                            <?php if (!empty($errors['name'])): ?>
                                <span class="error"><?php echo $errors['name']; ?></span>
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

                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                            <?php if (!empty($errors['message'])): ?>
                                <span class="error"><?php echo $errors['message']; ?></span>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn">Send Message</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.808559538267!2d36.8211893147539!3d-1.2888992990562136!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f10d664fecf0f%3A0x7d5e2a5e1a5e1a5e!2sAuto%20Elite!5e0!3m2!1sen!2ske!4v1620000000000!5m2!1sen!2ske" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>

<?php include 'includes/footer.php'; ?>