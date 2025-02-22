<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to checkout.");
}

$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart']; // Fetch cart data from session

// Check if the cart is empty
if (empty($cart_items)) {
    die("Your cart is empty. Cannot proceed with checkout.");
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    if (isset($item['price'], $item['quantity'])) {  // Ensure both price and quantity are set
        $total += $item['price'] * $item['quantity'];
    } else {
        die("Error: Invalid cart data.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../assets/css/checkout.css">
</head>
<body>
    <div class="checkout-wrapper">
        <h2>Checkout</h2>
        <div class="checkout-total">
            <p>Total: $<?php echo number_format($total, 2); ?></p>
        </div>
        <form action="process_checkout.php" method="POST">
            <button type="submit" class="checkout-btn">Confirm Order</button>
        </form>
    </div>
    <div class="footer">
        <p>Need help? <a href="../pages/contact.php">Contact Us</a></p>
    </div>
</body>
</html>
