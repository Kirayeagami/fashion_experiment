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

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    
    // Get user details before deletion for logging
    $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();
    
    if ($user) {
        // Delete user
        $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->bind_param("i", $user_id);
        
        if ($delete_stmt->execute()) {
            // Log the deletion
            AdminLogger::log($admin_id, 'User Deleted', "Deleted user: {$user['name']} (ID: {$user['id']})");
            $message = "User deleted successfully!";
        } else {
            $error = "Error deleting user: " . $conn->error;
        }
    }
}

// Fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/manage_users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="user-management-container">
        <h1 class="animate__animated animate__fadeInDown">Manage Users</h1>
        
        <?php if (isset($message)): ?>
            <div class="message success animate__animated animate__fadeIn"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error animate__animated animate__fadeIn"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="user-grid animate__animated animate__fadeInUp">
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-card">
                        <div class="user-info">
                            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                            <p>Role: <?php echo ucfirst($user['role']); ?></p>
                            <p>Joined: <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                        <div class="user-actions">
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                               class="btn edit-btn">Edit</a>
                            <a href="?delete=<?php echo $user['id']; ?>" 
                               class="btn delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-users">No users found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
