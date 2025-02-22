<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Redirect to login page if not logged in or if the user is not a seller
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: ../user/login.php");
    exit();
}

$seller_id = $_SESSION["user_id"];

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: seller_dashboard.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details
$product_stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$product_stmt->bind_param("ii", $product_id, $seller_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    echo "<p>Product not found or you do not have permission to edit it.</p>";
    exit();
}

$product = $product_result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target_dir = "../assets/images/";
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        $image = $product['image']; // Keep the old image if not updated
    }

    // Update product in the database
    $update_stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ? AND seller_id = ?");
    $update_stmt->bind_param("sdssii", $name, $price, $description, $image, $product_id, $seller_id);
    
    if ($update_stmt->execute()) {
        echo "<p>Product updated successfully.</p>";
        header("Location: seller_dashboard.php");
        exit();
    } else {
        echo "<p>Error updating product.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/edit_product.css">
</head>
<body>
    <h2>Edit Product</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label for="image">Product Image:</label>
        <input type="file" name="image">
        <p>Current Image:</p>
        <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" width="100" alt="Product Image">

        <button type="submit">Update Product</button>
    </form>
    <a href="seller_dashboard.php">Back to Dashboard</a>
</body>
</html>
