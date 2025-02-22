<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: ../pages/index.php");
    exit();
}

include __DIR__ . '/../includes/db.php'; // Database connection

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
    $upload_dir = "../assets/images/";
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
    <link rel="stylesheet" href="../assets/css/seller_add_product.css">
    <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="seller-add-product-container">
        <div class="seller-form-header">
            <h1>Add New Product</h1>
            <p>Fill in the details to add a new product</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        
        <form class="seller-product-form" action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="seller-form-group">
                <input type="text" id="product_name" name="product_name" required placeholder=" ">
                <label for="product_name">Product Name</label>
            </div>
            
            <div class="seller-form-group">
                <input type="number" id="price" name="price" step="0.01" required placeholder=" ">
                <label for="price">Price</label>
            </div>
            
            <div class="seller-form-group">
                <textarea id="description" name="description" required placeholder=" "></textarea>
                <label for="description">Description</label>
            </div>
            
            <div class="seller-form-group">
                <div class="image-upload-section">
                    <div class="seller-file-label" onclick="document.getElementById('product_image').click()">
                        <span>Upload Product Image</span>
                        <i class="fas fa-upload"></i>
                    </div>
                    <input type="file" id="product_image" name="product_image" accept="image/*" required onchange="previewImage(event)">
                    <div class="seller-image-preview" id="imagePreview">
                        <img src="" alt="Image Preview">
                        <div class="preview-text">Image Preview</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="seller-submit-btn">Add Product</button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const previewImg = preview.querySelector('img');
            const previewText = preview.querySelector('.preview-text');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.display = 'block';
                    previewImg.src = e.target.result;
                    previewText.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                previewImg.src = '';
                previewText.style.display = 'block';
            }
        }
    </script>
</body>
</html>
