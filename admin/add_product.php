<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $seller_id = 1; // Default admin seller ID

    // Handle image upload
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Insert product into database
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, category, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsssi", $name, $price, $description, $image_name, $category, $seller_id);
            
            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                $error = "Error adding product: " . $conn->error;
            }
        } else {
            $error = "Error uploading image.";
        }
    } else {
        $error = "Please select an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../assets/css/product_forms.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="product-form-container animate__animated animate__fadeIn">
        <div class="form-header">
            <h1>Add New Product</h1>
            <p>Fill in the details to add a new product</p>
        </div>
        
        <?php if (isset($message)): ?>
            <div class="message success animate__animated animate__fadeIn"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error animate__animated animate__fadeIn"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form class="product-form" action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" id="name" name="name" required placeholder=" ">
                <label for="name">Product Name</label>
            </div>
            
            <div class="form-group">
                <textarea id="description" name="description" required placeholder=" "></textarea>
                <label for="description">Description</label>
            </div>
            
            <div class="form-group">
                <input type="number" id="price" name="price" step="0.01" required placeholder=" ">
                <label for="price">Price</label>
            </div>
            
            <div class="form-group">
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Accessories">Accessories</option>
                </select>
                <label for="category">Category</label>
            </div>
            
            <div class="form-group">
                <div class="file-label" onclick="document.getElementById('image').click()">
                    Choose Product Image
                </div>
                <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(event)">
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Image Preview">
                </div>
            </div>

            <button type="submit" class="submit-btn">Add Product</button>
        </form>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const previewImg = preview.querySelector('img');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.display = 'block';
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                previewImg.src = '';
            }
        }
    </script>
</body>
</html>
