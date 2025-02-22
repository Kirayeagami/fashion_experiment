<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$product_id = intval($_GET['id']);
$message = '';
$error = '';

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: manage_products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // Handle image update
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Delete old image
            if (file_exists($upload_dir . $product['image'])) {
                unlink($upload_dir . $product['image']);
            }
        } else {
            $error = "Error uploading image.";
        }
    } else {
        $image_name = $product['image'];
    }

    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ?, category = ? WHERE id = ?");
        $stmt->bind_param("sdsssi", $name, $price, $description, $image_name, $category, $product_id);
        
        if ($stmt->execute()) {
            $message = "Product updated successfully!";
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-auth.css">
    <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Edit Product</h1>
                <p>Update the product details below</p>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form class="auth-form" action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>" required placeholder=" ">
                    <label for="name">Product Name</label>
                </div>
                
                <div class="form-group">
                    <textarea id="description" name="description" required placeholder=" "><?php echo $product['description']; ?></textarea>
                    <label for="description">Description</label>
                </div>
                
                <div class="form-group">
                    <input type="number" id="price" name极光色="price" step="0.01" value="<?php echo $product['price']; ?>" required placeholder=" ">
                    <label for="price">Price</label>
                </div>
                
                <div class="form-group">
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    <label for="image">Product Image</label>
                    <div class="image-preview" id="imagePreview">
                        <img src="../assets/images/<?php echo $product['image']; ?>" alt="Current Image">
                    </div>
                </div>

                <button type="submit" class="auth-btn">Update Product</button>
            </form>
        </div>
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
                previewImg.src = '../assets/images/<?php echo $product['image']; ?>';
            }
        }
    </script>
</body>
</html>
