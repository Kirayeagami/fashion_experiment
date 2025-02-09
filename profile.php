<?php
session_start();
include 'db.php'; // Ensure this includes the necessary database connection

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
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
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
                    <li><a href="seller_dashboard.php" class="btn dashboard-btn">Dashboard</a></li>
                    <li><a href="order_details.php" class="btn orders-btn">View Orders</a></li>
                    <li><a href="add_product.php" class="btn add-product-btn">Add New Product</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
