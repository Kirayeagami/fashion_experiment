<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Check if email already exists
            $checkStmt = $conn->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();
            
            if ($checkStmt->num_rows > 0) {
                $_SESSION['newsletter_message'] = 'You are already subscribed!';
            } else {
                // Insert new subscription
                $insertStmt = $conn->prepare("INSERT INTO newsletter_subscriptions (email, subscribed_at) VALUES (?, NOW())");
                $insertStmt->bind_param("s", $email);
                
                if ($insertStmt->execute()) {
                    $_SESSION['newsletter_message'] = 'Thank you for subscribing to our newsletter!';
                } else {
                    throw new Exception('Database error: ' . $conn->error);
                }
            }
        } catch (Exception $e) {
            $_SESSION['newsletter_error'] = 'An error occurred while processing your subscription. Please try again later.';
        }
    } else {
        $_SESSION['newsletter_error'] = 'Please enter a valid email address.';
    }
    
    // Redirect back to the previous page
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
