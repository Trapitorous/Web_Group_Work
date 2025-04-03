<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$page_title = "Manage Test Drives";
include 'header.php';

// Handle booking actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $booking_id = $_GET['id'] ?? 0;

    switch ($action) {
        case 'confirm':
            $stmt = $conn->prepare("UPDATE test_drives SET status = 'confirmed' WHERE id = ?");
            $stmt->bind_param("i", $booking_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Test drive confirmed";
            } else {
                $_SESSION['error'] = "Failed to confirm test drive";
            }
            break;

        case 'cancel':
            $stmt = $conn->prepare("UPDATE test_drives SET status = 'cancelled' WHERE id = ?");
            $stmt->bind_param("i", $booking_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Test drive cancelled";
            } else {
                $_SESSION['error'] = "Failed to cancel test drive";
            }
            break;

        case 'complete':
            $stmt = $conn->prepare("UPDATE test_drives SET status = 'completed' WHERE id = ?");
            $stmt->bind_param("i", $booking_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Test drive marked as completed";
            } else {
                $_SESSION['error'] = "Failed to update test drive status";
            }
            break;

        case 'delete':
            $stmt = $conn->prepare("DELETE FROM test_drives WHERE id = ?");
            $stmt->bind_param("i", $booking_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Test drive deleted";
            } else {
                $_SESSION['error'] = "Failed to delete test drive";
            }
            break;
    }

    header("Location: bookings.php");
    exit();
}

// Get all test drives
$test_drives = $conn->query("SELECT td.*, u.username, u.full_name, c.make, c.model 
                           FROM test_drives td 
                           JOIN users u ON td.user_id = u.id 
                           JOIN cars c ON td.car_id = c.id 
                           ORDER BY td.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Filter by status if specified
$status_filter = $_GET['status'] ?? 'all';
if ($status_filter !== 'all') {
    $test_drives = array_filter($test_drives, function($drive) use ($status_filter) {
        return $drive['status'] === $status_filter;
    });
}
?>

    <div class="admin-content">
        <div class="admin-header">
            <h2>Manage Test Drives</h2>

            <div class="filter-options">
                <span>Filter by status:</span>
                <a href="bookings.php?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
                <a href="bookings.php?status=pending" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="bookings.php?status=confirmed" class="<?php echo $status_filter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
                <a href="bookings.php?status=completed" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="bookings.php?status=cancelled" class="<?php echo $status_filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>User</th>
                    <th>Car</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Booked On</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($test_drives)): ?>
                    <tr>
                        <td colspan="6">No test drives found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($test_drives as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['full_name']); ?><br><?php echo htmlspecialchars($booking['username']); ?></td>
                            <td><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></td>
                            <td>
                                <?php echo date('M j, Y', strtotime($booking['scheduled_date'])); ?><br>
                                <?php echo date('g:i A', strtotime($booking['scheduled_time'])); ?>
                            </td>
                            <td><span class="status-badge status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                            <td>
                                <?php if ($booking['status'] == 'pending'): ?>
                                    <a href="bookings.php?action=confirm&id=<?php echo $booking['id']; ?>" class="btn small success">Confirm</a>
                                    <a href="bookings.php?action=cancel&id=<?php echo $booking['id']; ?>" class="btn small danger">Cancel</a>
                                <?php elseif ($booking['status'] == 'confirmed'): ?>
                                    <a href="bookings.php?action=complete&id=<?php echo $booking['id']; ?>" class="btn small">Complete</a>
                                <?php endif; ?>
                                <a href="bookings.php?action=delete&id=<?php echo $booking['id']; ?>" class="btn small danger" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php require_once '../includes/footer.php';?>