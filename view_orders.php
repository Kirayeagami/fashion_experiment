<?php
session_start();
include 'db.php'; // Ensure this file contains database connection

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    exit("Error: You must be logged in to view orders.");
}

$user_id = $_SESSION["user_id"];
$user_role = $_SESSION["user_role"]; // Assuming roles are stored in session

// Check if order_id is provided in URL
if (!isset($_GET['order_id'])) {
    exit("Error: Order ID is missing.");
}

$order_id = intval($_GET['order_id']);

// Check if the user is an admin or a seller with products in this order
if ($user_role === "seller") {
    $auth_stmt = $conn->prepare("SELECT COUNT(*) AS count FROM order_items oi 
                                 INNER JOIN products p ON oi.product_id = p.id
                                 WHERE oi.order_id = ? AND p.seller_id = ?");
    $auth_stmt->bind_param("ii", $order_id, $user_id);
    $auth_stmt->execute();
    $auth_result = $auth_stmt->get_result()->fetch_assoc();
    $auth_stmt->close();

    if ($auth_result['count'] == 0) {
        exit("Error: You are not authorized to view this order.");
    }
}

// Fetch order details
$order_stmt = $conn->prepare("SELECT o.order_id, o.user_id, o.total_price, o.created_at, u.name AS customer_name, u.email AS customer_email
                              FROM orders o
                              JOIN users u ON o.user_id = u.id
                              WHERE o.order_id = ?");
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order_stmt->close();

// Check if order exists
if ($order_result->num_rows == 0) {
    exit("Error: Order not found.");
}
$order = $order_result->fetch_assoc();

// Fetch order items
$order_items_stmt = $conn->prepare("SELECT oi.*, p.name AS product_name, p.price AS product_price, 
                                    p.image AS product_image, s.name AS seller_name 
                                    FROM order_items oi
                                    INNER JOIN products p ON oi.product_id = p.id
                                    INNER JOIN sellers s ON p.seller_id = s.id
                                    WHERE oi.order_id = ?");
$order_items_stmt->bind_param("i", $order_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();
$order_items_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - #<?php echo $order['order_id']; ?></title>
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
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>Customer Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
            <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
            <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i A", strtotime($order['created_at'])); ?></p>
        </div>

        <h3>Ordered Products:</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Seller</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $order_items_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="images/<?php echo htmlspecialchars($item['product_image']); ?>" alt="Product" class="product-image"></td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['seller_name']); ?></td>
                        <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>
