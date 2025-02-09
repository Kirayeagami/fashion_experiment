<?php
session_start();
include 'db.php';

// Redirect to login page if not logged in or if the user is not a seller
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: login.php");
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
$order_stmt = $conn->prepare("SELECT o.order_id, o.user_id, o.total_price, o.created_at
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="css/order_details.css">
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php">K I R A</a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="collections.php">Collections</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php">🛒 My Cart</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION["user_name"]); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="order-details-container">
            <h2>Order Details - Order #<?php echo $order['order_id']; ?></h2>

            <div class="order-info">
                <p><strong>Customer:</strong> 
                    <?php
                        // Fetch customer name using user_id from orders table
                        $customer_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
                        $customer_stmt->bind_param("i", $order['user_id']);
                        $customer_stmt->execute();
                        $customer_result = $customer_stmt->get_result();
                        if ($customer_result->num_rows > 0) {
                            $customer = $customer_result->fetch_assoc();
                            echo htmlspecialchars($customer['name']);
                        } else {
                            echo "Unknown Customer";
                        }                        
                    ?>
                </p>
                <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                <p><strong>Order Date:</strong> <?php echo date("F j, Y", strtotime($order['created_at'])); ?></p>
            </div>

            <h3>Ordered Products:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $order_items_result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="images/<?php echo $item['product_image']; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-image"></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>
