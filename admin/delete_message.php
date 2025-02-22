<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Check if message_id is set
if (isset($_POST['message_id'])) {
    $message_id = intval($_POST['message_id']);
    
    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM contact_inquiries WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    
    if ($stmt->execute()) {
        // Redirect back to view messages page
        header("Location: view_messages.php?success=Message deleted successfully.");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: view_messages.php?error=Failed to delete message.");
        exit();
    }
} else {
    // Redirect back with an error message if message_id is not set
    header("Location: view_messages.php?error=No message ID provided.");
    exit();
}
