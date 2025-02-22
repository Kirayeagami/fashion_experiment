<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: seller_dashboard.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$seller_id = $_SESSION["user_id"];

// Fetch order details
$order_stmt = $conn->prepare("SELECT o.order_id, o.total_price, o.created_at, u.name AS customer_name
                              FROM orders o
                              JOIN users u ON o.user_id = u.id
                              WHERE o.order_id = ?");
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows == 0) {
    echo "Order not found.";
    exit();
}
$order = $order_result->fetch_assoc();

// Fetch order items for this seller
$order_items_stmt = $conn->prepare("SELECT oi.*, p.name AS product_name, p.price AS product_price
                                     FROM order_items oi
                                     JOIN products p ON oi.product_id = p.id
                                     WHERE oi.order_id = ? AND p.seller_id = ?");
$order_items_stmt->bind_param("ii", $order_id, $seller_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../assets/css/order_confirmation.css">
</head>
<body>
    <div class="confirmation-container">
        <h2>Order Confirmation</h2>
        <p>Thank you for your order!</p>
        
        <div class="order-details">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
            <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i A", strtotime($order['created_at'])); ?></p>
        </div>

        <div class="order-items">
            <h3>Ordered Products</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $order_items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="seller_dashboard.php" class="btn">Return to Dashboard</a>
    </div>
</body>
</html>
