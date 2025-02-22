<?php
session_start();
include __DIR__ . '/../includes/db.php';

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
    <link rel="stylesheet" href="../assets/css/product_details.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <!-- Product Details -->
    <main>
        <section class="product-details-container">
            <div class="product-image">
                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>

            <div class="product-info">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                
                <!-- Wishlist Button -->
                <form action="../user/wishlist.php" method="POST" class="wishlist-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <?php
                    // Check if product is in wishlist
                    if (isset($_SESSION['user_id'])) {
                        $check_stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
                        $check_stmt->bind_param("ii", $_SESSION['user_id'], $product['id']);
                        $check_stmt->execute();
                        $in_wishlist = $check_stmt->get_result()->num_rows > 0;
                    }
                    ?>
                    <button type="submit" name="action" value="<?php echo isset($in_wishlist) && $in_wishlist ? 'remove' : 'add'; ?>" class="wishlist-btn">
                        <?php echo isset($in_wishlist) && $in_wishlist ? '❤️ Remove from Wishlist' : '🤍 Add to Wishlist'; ?>
                    </button>
                </form>

                <!-- Add to Cart Form -->
                <form action="../user/add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" class="quantity" required>
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        </section>
    </main>

    <script src="../assets/js/image-handler.js"></script>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
