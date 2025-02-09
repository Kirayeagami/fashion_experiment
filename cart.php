<?php
session_start();
include 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ensure the cart session exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle removal of item from cart
if (isset($_POST['remove'])) {
    $remove_id = $_POST['remove'];
    foreach ($_SESSION['cart'] as $index => $cart_item) {
        if ($cart_item['product_id'] == $remove_id) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            break;
        }
    }
}

// Handle updating the quantity of items in the cart
if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = intval($_POST['quantity']);

    if ($new_quantity > 0) {
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                $cart_item['quantity'] = $new_quantity;
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
    <title>My Cart</title>
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <h2>Your Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <!-- <th>Image</th> -->
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($_SESSION['cart'] as $cart_item): ?>
                    <?php
                    if (!isset($cart_item['product_id'], $cart_item['name'], $cart_item['price'], $cart_item['quantity'], $cart_item['image'])) {
                        continue; // Skip invalid cart items
                    }

                    $product_total = $cart_item['price'] * $cart_item['quantity'];
                    $total += $product_total;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cart_item['name']); ?></td>
                        <!-- <td>
                            <img src="<?php echo htmlspecialchars($cart_item['image']); ?>" 
                            alt="<?php echo htmlspecialchars($cart_item['name']); ?>" 
                            width="80" height="80">
                        </td> -->

                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($cart_item['product_id']); ?>">
                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($cart_item['quantity']); ?>" min="1" style="width: 50px;">
                                <button type="submit" name="update" class="update-btn">Update Quantity</button>
                            </form>
                        </td>
                        <td>$<?php echo number_format($cart_item['price'], 2); ?></td>
                        <td>$<?php echo number_format($product_total, 2); ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="remove" value="<?php echo htmlspecialchars($cart_item['product_id']); ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <p><strong>Total:</strong> $<?php echo number_format($total, 2); ?></p>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <p>Your cart is empty!</p>
        </div>
    <?php endif; ?>
</body>
</html>
