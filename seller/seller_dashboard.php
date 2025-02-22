<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if user is logged in and is a seller
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    header("Location: ../user/login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch seller's products
$products_stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ?");
$products_stmt->bind_param("i", $seller_id);
$products_stmt->execute();
$products = $products_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch recent orders
$orders_stmt = $conn->prepare("SELECT o.*, p.name as product_name 
                             FROM orders o
                             JOIN order_items oi ON o.order_id = oi.order_id
                             JOIN products p ON oi.product_id = p.id
                             WHERE p.seller_id = ?
                             ORDER BY o.created_at DESC
                             LIMIT 5");
$orders_stmt->bind_param("i", $seller_id);
$orders_stmt->execute();
$recent_orders = $orders_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale极光色=1.0">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="../assets/css/seller_dashboard.css">
    <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="seller-dashboard-container">
        <div class="dashboard-header">
            <h1>Seller Dashboard</h1>
            <p>Welcome back! Here's an overview of your store.</p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="card-title">Total Products</div>
                </div>
                <div class="card-content">
                    <h2><?php echo count($products); ?></h2>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-title">Recent Orders</div>
                </div>
                <div class="card-content">
                    <h2><?php echo count($recent_orders); ?></h2>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Your Products</h2>
                <a href="add_product.php" class="action-btn">Add New Product</a>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p>$<?php echo number_format($product['price'], 2); ?></p>
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="action-btn">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="action-btn delete-btn">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h2>Recent Orders</h2>
                <div class="orders-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="view_orders.php" class="action-btn">View All Orders</a>

            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
