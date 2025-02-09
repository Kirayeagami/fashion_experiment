<?php
session_start();
include 'db.php';

// Redirect if not logged in or not a seller
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION["user_id"];

// Fetch products added by the seller
$product_stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ?");
$product_stmt->bind_param("i", $seller_id);
$product_stmt->execute();
$products_result = $product_stmt->get_result();
$product_stmt->close();

// Fetch orders related to the seller's products
$order_stmt = $conn->prepare("
    SELECT o.order_id, o.user_id, o.total_price, o.created_at, u.name as customer_name
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    JOIN users u ON o.user_id = u.id
    WHERE p.seller_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$order_stmt->bind_param("i", $seller_id);
$order_stmt->execute();
$orders_result = $order_stmt->get_result();
$order_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (localStorage.getItem("darkMode") === "enabled") {
                document.body.classList.add("dark-mode");
            }

            document.getElementById("theme-toggle").addEventListener("click", function () {
                document.body.classList.toggle("dark-mode");
                localStorage.setItem("darkMode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
            });
        });

        function confirmDelete(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                window.location.href = "delete_product.php?id=" + productId;
            }
        }
    </script>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php">K I R A</a></div>
            <ul class="nav-links">
                <button id="theme-toggle" class="theme-toggle">🌙</button>
                <li><a href="index.php">Home</a></li>
                <li><a href="collections.php">Collections</a></li>
                <li><a href="http://localhost/e-comm/index.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php">🛒 My Cart</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION["user_name"]); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="dashboard-container">
            <h2>Welcome, Seller: <?php echo htmlspecialchars($_SESSION["user_name"]); ?></h2>

            <div class="dashboard-content">
                <div class="section">
                    <h3>Your Products</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $products_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $imagePath = 'images/' . $product['image'];
                                        echo file_exists($imagePath) ? "<img src='$imagePath' width='50' alt='Product Image'>" : "<img src='images/default.jpg' width='50' alt='Default Image'>";
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn edit-btn">Edit</a> |
                                        <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $product['id']; ?>)" class="btn delete-btn">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <a href="add_product.php" class="btn add-product-btn">Add New Product</a>
                </div>

                <div class="section">
                    <h3>Your Orders</h3>
                    <?php if ($orders_result->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Order Date</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                        <td><?php echo date("F j, Y", strtotime($order['created_at'])); ?></td>
                                        <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn">View Details</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No orders found for your products.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>