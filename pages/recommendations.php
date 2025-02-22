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

// Fetch product details
$product_id = $_GET['id']; // Get product ID from URL
$product_sql = "SELECT * FROM products WHERE id = '$product_id' LIMIT 1";

$product_result = $conn->query($product_sql);
$product = $product_result->fetch_assoc();

// SEO Improvements (Meta Tags)
echo "<title>" . $product['name'] . " | K I R A</title>";
echo "<meta name='description' content='" . substr($product['description'], 0, 150) . "...'>";
echo "<meta name='keywords' content='" . $product['name'] . ", buy " . $product['name'] . ", best " . $product['name'] . "'>";

// Fetch recommended products (same category or best sellers)
$recommend_sql = "SELECT * FROM products WHERE id != '$product_id' AND available = 1 ORDER BY RAND() LIMIT 4";

$recommend_result = $conn->query($recommend_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
