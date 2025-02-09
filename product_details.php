<?php
session_start();
include 'db.php';

// Check if the product ID is passed in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepared statement to fetch product details based on ID
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    if ($stmt === false) {
        echo "Error in preparing statement.";
        exit();
    }

    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if product exists
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    header("Location: collections.php"); // Redirect to collections if no ID is passed
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
    <link rel="stylesheet" href="css/product_details.css">
</head>
<body>

    <!-- Navigation Bar -->
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php">K I R A</a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="collections.php">Collections</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>

                <!-- Seller-specific links only visible for sellers -->
                <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "seller"): ?>
                    <li><a href="add_product.php">Add Product</a></li>
                    <li><a href="manage_products.php">Manage Products</a></li>
                    <li><a href="order_script.php">View Orders</a></li>
                <?php endif; ?>

                <li><a href="profile.php">Profile</a></li>
            </ul>
            <ul class="user-links">
                <li><a href="cart.php">🛒 My Cart</a></li>
                <?php if (isset($_SESSION["user_name"])): ?>                    
                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION["user_name"]); ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Product Details -->
    <main>
        <section class="product-details-container">
            <div class="product-image">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>

                <!-- Add to Cart Form -->
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" class="quantity" required>
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        </section>
    </main>

</body>
</html>
