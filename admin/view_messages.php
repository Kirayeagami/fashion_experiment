<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch messages from contact_inquiries table
$messages = $conn->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Messages</title>
    <!-- <link rel="stylesheet" href="../assets/css/admin.css"> -->
    <link rel="stylesheet" href="../assets/css/view_messages.css">

</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <div class="main-content" style="padding-top: 60px;"></div>
    <div class="admin-container messages-container">

        <h1>User Messages</h1>
        <?php if (!empty($messages)): ?>
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['message']); ?></td>
                            <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                            <td>
                                <form action="delete_message.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No messages found.</p>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
