<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Ensure product ID and quantity are set
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Fetch product details, including image
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        // Prepare cart item
        $cart_item = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image'] // Ensure image is added
        ];

        // Initialize the cart session if not set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if product exists in cart and update quantity
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] += $quantity; // Update quantity
                $found = true;
                break;
            }
        }

        // Add new product if not found
        if (!$found) {
            $_SESSION['cart'][] = $cart_item;
        }

        // Redirect to cart page
        header("Location: cart.php");
        exit();
    } else {
        echo "Product not found!";
    }
} else {
    echo "Invalid request!";
}
?>
