<?php
session_start();
include __DIR__ . '/../includes/db.php';
require 'AdminLogger.php';

$message = ""; // Variable to store alert messages
$error = ""; // Error message for incorrect login

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $admin["password_hash"])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store admin data in session
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];

            // Log the login activity
            AdminLogger::log($admin['id'], 'Login', 'Successful login');

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect username or password!";
            AdminLogger::log(0, 'Failed Login', "Failed login attempt for username: $username");
        }
    } else {
        $error = "Incorrect username or password!";
        AdminLogger::log(0, 'Failed Login', "Failed login attempt for username: $username");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-auth.css">
    <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2>Admin Login</h2>
                <p>Please enter your credentials to access the admin panel</p>
            </div>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form class="auth-form" action="login.php" method="POST">
                <div class="form-group">
                    <input type="text" id="username" name="username" required placeholder=" ">
                    <label for="username">Username</label>
                </div>
                
                <div class="form-group">
                    <input type="password" id="password" name="password" required placeholder=" ">
                    <label for="password">Password</label>
                </div>
                
                <button type="submit" class="auth-btn">Login</button>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
