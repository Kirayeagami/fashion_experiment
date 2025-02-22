<?php
session_start();
include __DIR__ . '/../includes/db.php'; // Ensure db.php correctly initializes the $conn database connection

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Invalid request method.");
}

// Ensure user is logged in and cart is not empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    die("Error: You must be logged in and have items in your cart.");
}

$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart'];

$total_price = 0;
$order_items = [];

// Prepare statement to get product price and seller ID
$stmt = $conn->prepare("SELECT price, seller_id FROM products WHERE id = ?");
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

// Start database transaction
$conn->begin_transaction();

try {
    foreach ($cart_items as $item) {
        if (!isset($item['product_id']) || !isset($item['quantity'])) {
            continue; // Skip invalid items
        }

        $product_id = (int) $item['product_id'];
        $quantity = (int) $item['quantity'];

        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $total_price += $product['price'] * $quantity;
            $order_items[] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'seller_id' => $product['seller_id']
            ];
        } else {
            throw new Exception("Error: Product with ID $product_id not found.");
        }
    }
    $stmt->close();

    // Ensure there are valid order items
    if (empty($order_items)) {
        throw new Exception("Error: No valid products found in cart.");
    }

    // Insert order into the orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, created_at) VALUES (?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Error preparing order query: " . $conn->error);
    }

    $stmt->bind_param("id", $user_id, $total_price);
    if (!$stmt->execute()) {
        throw new Exception("Error placing order: " . $stmt->error);
    }

    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items into order_items table
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, seller_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error preparing order items query: " . $conn->error);
    }

    foreach ($order_items as $item) {
        if (empty($item['seller_id'])) {
            throw new Exception("Error: Seller ID missing for product ID {$item['product_id']}.");
        }

        $stmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['seller_id']);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting order item: " . $stmt->error);
        }
    }
    $stmt->close();

    // Clear cart from session
    unset($_SESSION['cart']);

    // Commit transaction
    $conn->commit();

    // Redirect to success page
    header("Location: success.php?order_id=" . $order_id);
    exit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    die("Error: " . $e->getMessage());
}
?>
