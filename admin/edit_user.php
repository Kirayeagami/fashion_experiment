<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['id']);
$message = '';
$error = '';

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);
        
        if ($stmt->execute()) {
            $message = "User updated successfully!";
        } else {
            $error = "Error updating user: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <!-- <link rel="stylesheet" href="../assets/css/admin.css"> -->
    <link rel="stylesheet" href="../assets/css/admin-auth.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Edit User</h1>
                <p>Update the user details below</p>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form class="auth-form" action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">

                <div class="form-group">
                    <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required placeholder=" ">
                    <label for="name">Name</label>
                </div>
                
                <div class="form-group">
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required placeholder=" ">
                    <label for="email">Email</label>
                </div>
                
                <div class="form-group">
                    <select id="role" name="role" required>
                        <option value="customer" <?php echo ($user['role'] === 'customer') ? 'selected' : ''; ?>>Customer</option>
                        <option value="seller" <?php echo ($user['role'] === 'seller') ? 'selected' : ''; ?>>Seller</option>
                    </select>
                    <label for="role">Role</label>
                </div>
                
                <button type="submit" class="auth-btn">Update User</button>
            </form>
        </div>
    </div>

</body>
</html>
