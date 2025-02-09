<?php
session_start();
include 'db.php'; // Ensure this file connects to your database

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Error: This page must be accessed via a POST request.");
}

// Ensure user is logged in and cart is not empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    exit("Error: You must be logged in and have items in your cart.");
}

$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart'];

$total_price = 0; // ✅ Fixed: Added $
$order_items = [];

// Prepare statement to fetch product details
$stmt = $conn->prepare("SELECT price, seller_id FROM products WHERE id = ?");
if (!$stmt) {
    exit("Error preparing query: " . $conn->error);
}

foreach ($cart_items as $item) {
    if (!isset($item['product_id']) || !isset($item['quantity'])) {
        continue;
    }
    
    $product_id = (int)$item['product_id'];
    $quantity = (int)$item['quantity'];

    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        if (!isset($product['seller_id'])) { // ✅ Prevent error if seller_id is missing
            exit("Error: Seller ID missing for product ID $product_id.");
        }

        $total_price += $product['price'] * $quantity;
        $order_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'seller_id' => $product['seller_id']
        ];
    }
}
$stmt->close();

// Ensure we have valid order items
if (empty($order_items)) {
    exit("Error: No valid products in cart.");
}

// Insert order into orders table
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, created_at) VALUES (?, ?, NOW())");
if (!$stmt) {
    exit("Error preparing order query: " . $conn->error);
}

$stmt->bind_param("id", $user_id, $total_price);
if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, seller_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        exit("Error preparing order items query: " . $conn->error);
    }
    
    foreach ($order_items as $item) {
        $stmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['seller_id']);
        if (!$stmt->execute()) {
            exit("Error inserting order item: " . $conn->error);
        }
    }
    $stmt->close();
    
    // Clear cart and redirect to confirmation page
    unset($_SESSION['cart']);
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit(); // ✅ Ensure script stops after redirect
} else {
    exit("Error placing order: " . $conn->error);
}
?>
