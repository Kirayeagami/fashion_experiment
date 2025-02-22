<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Redirect to login page if not logged in or if the user is not a seller
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: ../user/login.php");
    exit();
}

// Check if order ID is provided in the URL
if (!isset($_GET['order_id'])) {
    header("Location: seller_dashboard.php");
    exit();
}

// Fetch order details
$order_id = $_GET['order_id'];
$seller_id = $_SESSION["user_id"];

// Get the order information where the seller's products are involved
$order_stmt = $conn->prepare("SELECT o.order_id, o.user_id, o.total_price, o.created_at, o.status
                              FROM orders o
                              JOIN order_items oi ON o.order_id = oi.order_id
                              WHERE oi.product_id IN (SELECT id FROM products WHERE seller_id = ?)
                              AND o.order_id = ?");
$order_stmt->bind_param("ii", $seller_id, $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

// Check if order exists
if ($order_result->num_rows == 0) {
    echo "Order not found or you do not have permission to view it.";
    exit();
}
$order = $order_result->fetch_assoc();

// Get order items related to the seller's products
$order_items_stmt = $conn->prepare("SELECT oi.*, 
                                           p.name AS product_name, 
                                           p.price AS product_price, 
                                           p.image AS product_image, 
                                           (oi.quantity * p.price) AS total_price 
                                    FROM order_items oi
                                    INNER JOIN products p ON oi.product_id = p.id
                                    WHERE oi.order_id = ? AND p.seller_id = ?");
$order_items_stmt->bind_param("ii", $order_id, $seller_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();

// Fetch customer information
$customer_stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$customer_stmt->bind_param("i", $order['user_id']);
$customer_stmt->execute();
$customer_result = $customer_stmt->get_result();
$customer = $customer_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../assets/css/order_details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <main class="order-details-container">
        <div class="order-header">
            <h2>Order Details - Order #<?php echo $order['order_id']; ?></h2>
            <button class="print-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Print Order
            </button>
        </div>

        <div class="order-info-grid">
            <div class="order-info">
                <h3>Order Information</h3>
                <p><strong>Status:</strong> 
                    <span class="order-status <?php echo strtolower($order['status']); ?>">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </p>
                <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?></p>
                <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
            </div>

            <div class="customer-info">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
            </div>
        </div>

        <div class="order-products">
            <h3>Ordered Products</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $order_items_result->fetch_assoc()): ?>
                        <tr>
                            <td class="product-info">
                                <img src="../assets/images/<?php echo $item['product_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                     class="product-image">
                                <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                            </td>
                            <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="order-actions">
            <button class="btn btn-primary" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </button>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
