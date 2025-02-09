<?php
session_start();  // Start the session to access session variables
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful</title>
    <link rel="stylesheet" href="css/success.css">
</head>
<body>
    <div class="success-container">
        <h2>🎉 Order Successful!</h2>
        <p>Thank you for shopping with us. Your order has been placed.</p>   
        <?php if (isset($_SESSION["user_name"])): ?>
            <p>Thank you, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>, for shopping with us. Your order has been placed.</p>
        <?php else: ?>
            <p>Thank you for shopping with us. Your order has been placed.</p>
        <?php endif; ?>

        <a href="collections.php" class="back-btn">Continue Shopping</a>
    </div>
</body>
</html>
