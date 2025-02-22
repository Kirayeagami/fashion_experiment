<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle add/remove from wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];
    
    if ($action === 'add') {
        // Check if already in wishlist
        $check_stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
        $check_stmt->bind_param("ii", $user_id, $product_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows === 0) {
            // Add to wishlist
            $insert_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $insert_stmt->bind_param("ii", $user_id, $product_id);
            $insert_stmt->execute();
            
            // Update wishlist count
            $update_stmt = $conn->prepare("UPDATE products SET wishlist_count = wishlist_count + 1 WHERE id = ?");
            $update_stmt->bind_param("i", $product_id);
            $update_stmt->execute();
        }
    } elseif ($action === 'remove') {
        // Remove from wishlist
        $delete_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $product_id);
        $delete_stmt->execute();
        
        // Update wishlist count
        $update_stmt = $conn->prepare("UPDATE products SET wishlist_count = wishlist_count - 1 WHERE id = ?");
        $update_stmt->bind_param("i", $product_id);
        $update_stmt->execute();
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Fetch user's wishlist
$wishlist_stmt = $conn->prepare("SELECT p.* FROM wishlist w 
                               JOIN products p ON w.product_id = p.id 
                               WHERE w.user_id = ?");
$wishlist_stmt->bind_param("i", $user_id);
$wishlist_stmt->execute();
$wishlist = $wishlist_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale极光色=1.0">
    <title>My Wishlist</title>
    <link rel="stylesheet" href="../assets/css/wishlist.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="wishlist-container">
        <h2>My Wishlist</h2>
        
        <?php if (!empty($wishlist)): ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlist as $product): ?>
                    <div class="wishlist-item">
                        <a href="../pages/product_details.php?id=<?php echo $product['id']; ?>">
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>$<?php echo number_format($product['price'], 2); ?></p>
                        <form action="wishlist.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="remove-btn">Remove from Wishlist</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-wishlist">Your wishlist is empty.</p>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
