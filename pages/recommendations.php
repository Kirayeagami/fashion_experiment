<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "fashion";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product details securely using prepared statements
$product_id = $_GET['id']; // Get product ID from URL

// Prepare statement for product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

// SEO Improvements (Meta Tags)
echo "<title>" . htmlspecialchars($product['name']) . " | K I R A</title>";
echo "<meta name='description' content='" . htmlspecialchars(substr($product['description'], 0, 150)) . "...'>";
echo "<meta name='keywords' content='" . htmlspecialchars($product['name']) . ", buy " . htmlspecialchars($product['name']) . ", best " . htmlspecialchars($product['name']) . "'>";

// Fetch recommended products securely using prepared statements
$recommend_stmt = $conn->prepare("SELECT * FROM products WHERE id != ? AND available = 1 ORDER BY RAND() LIMIT 4");
$recommend_stmt->bind_param("i", $product_id);
$recommend_stmt->execute();
$recommend_result = $recommend_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://i.ibb.co/7NV0mNm9/IMG-20230803-013043224-transformed-x4-x16.jpg" type="image/png" />
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="product-container">
        <h1><?php echo $product['name']; ?></h1>
        <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
        <p><?php echo $product['description']; ?></p>
        <p>Price: $<?php echo $product['price']; ?></p>
    </div>
    
    <div class="recommendations">
        <h2>Recommended Products</h2>
        <div class="product-list">
            <?php while ($row = $recommend_result->fetch_assoc()) { ?>
                <div class="product-item">
                    <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                    <p><?php echo $row['name']; ?></p>
                    <p>Price: $<?php echo $row['price']; ?></p>
                    <a href="product.php?id=<?php echo $row['id']; ?>">View Product</a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
