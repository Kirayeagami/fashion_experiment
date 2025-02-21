<?php
session_start();
include __DIR__ . '/../includes/db.php';

error_log("Incoming POST data: " . print_r($_POST, true)); // Log incoming POST data
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
        error_log("Product details fetched: " . print_r($product, true)); // Log product details

        // Prepare cart item
        $cart_item = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,

            'image' => $product['image'] // Ensure image is added
        ];

        error_log("Current cart state: " . print_r($_SESSION['cart'], true)); // Log current cart state
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

    // Update product stock in the database
    $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    $stmt->bind_param("ii", $quantity, $product_id);
    if (!$stmt->execute()) {
        error_log("Failed to update product quantity in database: " . $stmt->error); // Log any errors
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
