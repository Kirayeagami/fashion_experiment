<?php
session_start();
include __DIR__ . '/../includes/db.php'; // Ensure this includes the necessary database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <div class="main-content" style="padding-top: 120px;">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <div class="profile-container">
        <div class="profile-header">
            <h1>User Profile</h1>
            <p>Welcome back, <?php echo htmlspecialchars($user_data['name']); ?>!</p>
        </div>

        <div class="profile-details">
            <div class="detail">
                <h3>Name:</h3>
                <p><?php echo htmlspecialchars($user_data['name']); ?></p>
            </div>
            <div class="detail">
                <h3>Email:</h3>
                <p><?php echo htmlspecialchars($user_data['email']); ?></p>
            </div>
            <div class="detail">
                <h3>Role:</h3>
                <p><?php echo htmlspecialchars($user_data['role']); ?></p>
            </div>
        </div>

        <div class="actions">
            <a href="edit_profile.php" class="btn">Edit Profile</a>
            <a href="logout.php" class="btn logout-btn">Logout</a>
        </div>

        <?php if ($user_data['role'] === 'seller'): ?>
            <!-- Seller Dashboard Section -->
            <div class="seller-dashboard">
                <h3>Seller Dashboard</h3>
                <ul>
                    <li><a href="../seller/seller_dashboard.php" class="btn dashboard-btn">Dashboard</a></li>
                    <li><a href="../seller/view_orders.php" class="btn orders-btn">View Orders</a></li>
                    <li><a href="../seller/add_product.php" class="btn add-product-btn">Add New Product</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php
        // Fetch wishlist products for the user
        $wishlist_stmt = $conn->prepare("
            SELECT p.id, p.name, p.price, p.image
            FROM wishlist w
            JOIN products p ON w.product_id = p.id
            WHERE w.user_id = ?
        ");
        $wishlist_stmt->bind_param("i", $user_id);
        $wishlist_stmt->execute();
        $wishlist_items = $wishlist_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>

        <?php if (!empty($wishlist_items)): ?>
            <div class="wishlist-section">
                <h3>Your Wishlist</h3>
                <div class="wishlist-items">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="wishlist-item">
                            <a href="../pages/product_details.php?id=<?php echo $item['id']; ?>" style="text-decoration:none; color:inherit;">
                                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                                <p class="product-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="product-price">$<?php echo number_format($item['price'], 2); ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p>You have no items in your wishlist.</p>
        <?php endif; ?>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
