<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: index.php");
    exit();
}

include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["product_name"]);
    $price = $_POST["price"];
    $desc = htmlspecialchars($_POST["description"]);
    $seller_id = $_SESSION["user_id"];

    // Check if seller exists in the sellers table
    $check_seller = $conn->prepare("SELECT seller_id FROM sellers WHERE seller_id = ?");
    $check_seller->bind_param("i", $seller_id);
    $check_seller->execute();
    $result = $check_seller->get_result();

    if ($result->num_rows === 0) {
        echo "<p class='error'>Invalid seller account.</p>";
        exit();
    }

    // Handle image upload
    $upload_dir = "images/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image = $_FILES["product_image"]["name"];
    $image_tmp = $_FILES["product_image"]["tmp_name"];
    $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $valid_image_types = ["jpg", "jpeg", "png", "gif"];

    if (!in_array($imageFileType, $valid_image_types)) {
        echo "<p class='error'>Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.</p>";
        exit();
    }

    if ($_FILES["product_image"]["size"] > 5000000) {
        echo "<p class='error'>File is too large. Max size is 5MB.</p>";
        exit();
    }

    $new_filename = uniqid("img_", true) . "." . $imageFileType;
    $target_file = $upload_dir . $new_filename;

    if (!move_uploaded_file($image_tmp, $target_file)) {
        echo "<p class='error'>Error uploading file. Please try again.</p>";
        exit();
    }

    // Insert product into database
    $stmt = $conn->prepare("INSERT INTO products (seller_id, name, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $seller_id, $name, $price, $desc, $new_filename);

    if ($stmt->execute()) {
        echo "<p class='success'>Product added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/add_product.css">
    <nav class="navbar">
        <h1 class="navbar-heading">K I R A</h1>
        <ul class="navbar-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php">About</a></li>
            <li><a href="#social">Contact</a></li>
        </ul>
    </nav>
</head>
<body>
    <div class="container">
        <h2>Add a New Product</h2>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="product_name" placeholder="Product Name" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <textarea name="description" placeholder="Product Description" required></textarea>
            <input type="file" name="product_image" required>
            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>
