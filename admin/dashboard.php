<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch statistics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_price) as total FROM orders")->fetch_assoc()['total'];

// Fetch messages from contact_inquiries table
$messages = $conn->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <script src="../assets/js/header.js" defer></script>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $total_products; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p><?php echo $total_orders; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($total_revenue, 2); ?></p>
            </div>
        </div>
        
        <div class="admin-links">
            <a href="view_messages.php" class="admin-link">View Messages</a>

            <a href="manage_products.php" class="admin-link">Manage Products</a>
            <a href="manage_orders.php" class="admin-link">Manage Orders</a>
            <a href="manage_users.php" class="admin-link">Manage Users</a>
            <a href="create_admin.php" class="admin-link">Create Admin</a>
            <a href="activity_log.php" class="admin-link">View Activity Log</a>
            <a href="logout.php" class="admin-link">Logout</a>
        </div>
    </div>

        <!-- Messages Section -->
        <!-- <div class="messages-section">
            <h2>User Messages</h2>
            <?php if (!empty($messages)): ?>
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['message']); ?></td>
                                <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No messages found.</p>
            <?php endif; ?>
        </div> -->

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
