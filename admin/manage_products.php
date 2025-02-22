<?php
session_start();
include __DIR__ . '/../includes/db.php';
require 'AdminLogger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    
    // Get product details before deletion for logging
    $product_stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product = $product_stmt->get_result()->fetch_assoc();
    
    if ($product) {
        // Delete product
        $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $delete_stmt->bind_param("i", $product_id);
        
        if ($delete_stmt->execute()) {
            // Log the deletion
            AdminLogger::log($admin_id, 'Product Deleted', "Deleted product: {$product['name']} (ID: {$product['id']})");
            $message = "Product deleted successfully!";
        } else {
            $error = "Error deleting product: " . $conn->error;
        }
    }
}

// Fetch all products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/manage_products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="manage-products-container">
        <h1 class="animate__animated animate__fadeInDown">Manage Products</h1>
        
        <?php if (isset($message)): ?>
            <div class="message success animate__animated animate__fadeIn"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error animate__animated animate__fadeIn"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <a href="add_product.php" class="admin-link animate__animated animate__fadeIn">Add New Product</a>
        
        <div class="product-grid animate__animated animate__fadeInUp">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="product-image">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                   class="btn edit-btn">Edit</a>
                                <a href="?delete=<?php echo $product['id']; ?>" 
                                   class="btn delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">No products found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
