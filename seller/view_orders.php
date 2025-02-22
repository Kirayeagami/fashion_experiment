<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if user is logged in and is a seller
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    header("Location: ../user/login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch all orders for the seller
$orders_stmt = $conn->prepare("SELECT o.order_id, o.user_id, o.total_price, o.created_at, o.status,
                                u.name AS customer_name, u.email AS customer_email
                              FROM orders o
                              JOIN order_items oi ON o.order_id = oi.order_id
                              JOIN products p ON oi.product_id = p.id
                              JOIN users u ON o.user_id = u.id
                              WHERE p.seller_id = ?
                              GROUP BY o.order_id
                              ORDER BY o.created_at DESC");
$orders_stmt->bind_param("i", $seller_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="../assets/css/view_orders.css">

</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="orders-container">
    <h1>Your Orders</h1>

    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($order['customer_name']); ?><br>
                            <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                        </td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td><?php echo date("M j, Y", strtotime($order['created_at'])); ?></td>
                        <td>
                            <span class="order-status <?php echo strtolower($order['status']); ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" 
                               class="action-btn">
                                View Details
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
