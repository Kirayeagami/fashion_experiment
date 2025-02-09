<?php
session_start();
include 'db.php';

// Ensure the user is logged in, if not set "Guest"
$user_name = isset($_SESSION["user_name"]) ? htmlspecialchars($_SESSION["user_name"]) : "Guest";

// Prepared statement to fetch products (to prevent SQL injection)
$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="css/collections.css">
</head>
<body>

    <!-- Navigation Bar -->
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php">K I R A</a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="collections.php">Collections</a></li>
                <li><a href="http://localhost/e-comm/index.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <!-- The 'Add Product', 'Manage Products', and 'View Orders' options are only visible to sellers -->
                <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "seller"): ?>
                    <li><a href="add_product.php">Add Product</a></li>
                    <li><a href="seller_dashboard.php">Dashboard</a></li>
                    <li><a href="order_details.php">View Orders</a></li>
                <?php endif; ?>
            </ul>
            <ul class="user-links">
                <!-- Cart link is visible to everyone -->
                <li><a href="cart.php">🛒 My Cart</a></li>

                <?php if (isset($_SESSION["user_name"])): ?>
                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION["user_name"]); ?>)</a></li>
                    <!-- Seller-specific links are visible only for sellers -->
                    <?php if ($_SESSION["user_role"] === "seller"): ?>
                        <!-- Additional seller options can go here -->
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <button id="theme-toggle" class="theme-toggle">🌙</button>
    </header>

    <main>
        <section class="products-container">
            <h2>Our Products</h2>
            <div class="product-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <!-- Make image clickable -->
                        <a href="product_details.php?id=<?php echo $row['id']; ?>">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </a>

                        <!-- Make product name clickable -->
                        <h3><a href="product_detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a></h3>
                        <p>$<?php echo number_format($row['price'], 2); ?></p>

                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="quantity" required>
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

</body>
</html>
