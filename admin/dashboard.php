<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../admin/header.php';
requireAdmin();

$page_title = "Admin Dashboard";
include '../includes/header.php';

// Get counts for dashboard
$users_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$cars_count = $conn->query("SELECT COUNT(*) FROM cars")->fetch_row()[0];
$test_drives_count = $conn->query("SELECT COUNT(*) FROM test_drives")->fetch_row()[0];
$pending_test_drives = $conn->query("SELECT COUNT(*) FROM test_drives WHERE status = 'pending'")->fetch_row()[0];

// Get recent test drives
$recent_test_drives = $conn->query("SELECT td.*, u.username, c.make, c.model 
                                   FROM test_drives td 
                                   JOIN users u ON td.user_id = u.id 
                                   JOIN cars c ON td.car_id = c.id 
                                   ORDER BY td.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

    <section class="admin-dashboard">
        <h2>Dashboard Overview</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <p><?php echo $users_count; ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Cars</h3>
                    <p><?php echo $cars_count; ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Test Drives</h3>
                    <p><?php echo $test_drives_count; ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Pending Test Drives</h3>
                    <p><?php echo $pending_test_drives; ?></p>
                </div>
            </div>
        </div>

        <div class="recent-activities">
            <h3>Recent Test Drive Bookings</h3>

            <?php if (empty($recent_test_drives)): ?>
                <p>No recent test drive bookings.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Car</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recent_test_drives as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['username']); ?></td>
                            <td><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($booking['scheduled_date'])); ?></td>
                            <td><?php echo date('g:i A', strtotime($booking['scheduled_time'])); ?></td>
                            <td><span class="status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></td>
                            <td>
                                <a href="bookings.php?action=view&id=<?php echo $booking['id']; ?>" class="btn small">View</a>
                                <?php if ($booking['status'] == 'pending'): ?>
                                    <a href="bookings.php?action=confirm&id=<?php echo $booking['id']; ?>" class="btn small success">Confirm</a>
                                    <a href="bookings.php?action=cancel&id=<?php echo $booking['id']; ?>" class="btn small danger">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>