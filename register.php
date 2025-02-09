<?php
session_start();
include 'db.php';

$message = ""; // Variable to store alert messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $phone = isset($_POST["phone"]) ? $_POST["phone"] : NULL;  // Capture phone, and allow it to be NULL if not provided
    $role = $_POST["role"]; // Capture role from form input

    // Validate role to prevent SQL errors
    if ($role !== "customer" && $role !== "seller") {
        $message = "Invalid role selected.";
    } else {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        } else {
            // Check if email already exists
            $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $result = $check_email->get_result();

            if ($result->num_rows > 0) {
                $message = "Email already exists!";
            } else {
                // Hash password and prepare query to insert user into users table
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $password_hashed, $phone, $role);  // Bind phone (nullable)

                if ($stmt->execute()) {
                    // Get the last inserted user ID
                    $user_id = $stmt->insert_id;

                    // If the role is 'seller', insert into sellers table
                    if ($role === "seller") {
                        // Use the user_id as seller_id and pass business_name (same as name here)
                        $stmt_seller = $conn->prepare("INSERT INTO sellers (seller_id, business_name, email, phone) VALUES (?, ?, ?, ?)");
                        $stmt_seller->bind_param("isss", $user_id, $name, $email, $phone);  // Correct bind_param for 4 variables

                        if ($stmt_seller->execute()) {
                            $message = "Seller registration successful!";
                        } else {
                            $message = "Error adding seller details: " . $stmt_seller->error;
                        }
                    } else {
                        $message = "User registration successful!";
                    }
                } else {
                    $message = "Error: " . $stmt->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        
        <!-- Display success or error message if there's one -->
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Registration Form -->
        <form action="register.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="phone" placeholder="Phone Number"> <!-- Optional phone input -->
            
            <!-- Role Selection -->
            <select name="role" required>
                <option value="customer">Customer</option>
                <option value="seller">Seller</option>
            </select>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>
