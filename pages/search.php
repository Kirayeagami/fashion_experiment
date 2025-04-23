<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Get search query
$query = $_GET['q'] ?? '';

// Prepare search query
$searchQuery = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
$stmt = $conn->prepare($searchQuery);
$searchTerm = "%$query%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="shortcut icon" href="https://i.ibb.co/7NV0mNm9/IMG-20230803-013043224-transformed-x4-x16.jpg" type="image/png" />
    <link rel="stylesheet" href="../assets/css/search.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="search-results">
        <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
        
        <?php if (!empty($results)): ?>
            <div class="results-grid">
                <?php foreach ($results as $product): ?>
                    <div class="product-card">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>">
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>$<?php echo number_format($product['price'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No products found matching your search.</p>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>