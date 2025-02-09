<?php
session_start();
include 'db.php';

// Check if the user is logged in as a seller
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: index.php");
    exit();
}

// Get the product ID from the URL parameter
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the product exists and belongs to the current seller
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Delete product image file if it exists
        $imagePath = 'images/' . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Remove the product image from the server
        }

        // First, delete the related order items
        $deleteOrderItemsStmt = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
        $deleteOrderItemsStmt->bind_param("i", $product_id);
        $deleteOrderItemsStmt->execute();

        // Now, delete the product from the database
        $deleteProductStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $deleteProductStmt->bind_param("i", $product_id);
        $deleteProductStmt->execute();

        // Redirect to manage products page after deletion
        header("Location: manage_products.php");
        exit();
    } else {
        // If no product found or doesn't belong to the seller, redirect to manage products page
        header("Location: manage_products.php");
        exit();
    }
} else {
    // If no product ID is passed, redirect to manage products page
    header("Location: manage_products.php");
    exit();
}
?>
