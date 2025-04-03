<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php'; // Added this line
requireAdmin();

$page_title = "Manage Users";
include 'header.php';

// Handle user actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $user_id = $_GET['id'] ?? 0;

    switch ($action) {
        case 'delete':
            if ($user_id == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot delete your own account";
            } else {
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "User deleted successfully";
                } else {
                    $_SESSION['error'] = "Failed to delete user";
                }
            }
            break;

        case 'toggle_admin':
            if ($user_id == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot modify your own admin status";
            } else {
                $user = getUserDetails($user_id);
                if ($user) {
                    $is_admin = $user['is_admin'] ? 0 : 1;
                    $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
                    $stmt->bind_param("ii", $is_admin, $user_id);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = "User admin status updated";
                    } else {
                        $_SESSION['error'] = "Failed to update user admin status";
                    }
                }
            }
            break;
    }

    header("Location: users.php");
    exit();
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

    <div class="admin-content">
        <h2>Manage Users</h2>

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
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['is_admin']): ?>
                                <span class="badge admin">Admin</span>
                            <?php else: ?>
                                <span class="badge user">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$user['is_admin'] || $user['id'] == $_SESSION['user_id']): ?>
                                <a href="#" class="btn small disabled">Make Admin</a>
                            <?php else: ?>
                                <a href="users.php?action=toggle_admin&id=<?php echo $user['id']; ?>" class="btn small <?php echo $user['is_admin'] ? 'warning' : 'success'; ?>">
                                    <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                <a href="#" class="btn small danger disabled">Delete</a>
                            <?php else: ?>
                                <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn small danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php require_once '../includes/footer.php';?>