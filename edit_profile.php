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
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Handle the form submission to update the user profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Validate inputs
    if (empty($name) || empty($email)) {
        $error_message = "Name and Email are required.";
    } else {
        // Update user details in the database
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);

        if ($stmt->execute()) {
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Failed to update profile. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/edit_profile.css">
</head>
<body>
    <div class="edit-profile-container">
        <div class="edit-profile-header">
            <h1>Edit Your Profile</h1>
        </div>

        <div class="edit-profile-form">
            <?php if (isset($success_message)) { ?>
                <div class="alert success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php } ?>

            <?php if (isset($error_message)) { ?>
                <div class="alert error">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php } ?>

            <form action="edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Update Profile</button>
                    <a href="profile.php" class="btn cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
