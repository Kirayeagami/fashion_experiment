<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Initialize variables
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'price_asc';
$search = $_GET['search'] ?? '';

// Build base query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = '';

// Add category filter
if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

// Add search filter
if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'name_asc':
        $query .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY name DESC";
        break;
    default:
        $query .= " ORDER BY created_at DESC";
}

// Prepare and execute query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Collections</title>
    <link rel="stylesheet" href="../assets/css/collections.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="collections-container">
        <h2 class="animate__animated animate__fadeInDown">Our Collections</h2>
        
        <!-- Filters and Sorting -->
        <div class="filters-sorting animate__animated animate__fadeIn">
            <form method="GET" class="filters-form">
                <select name="category">
                    <option value="">All Categories</option>
                    <option value="clothing" <?php echo ($category === 'clothing') ? 'selected' : ''; ?>>Clothing</option>
                    <option value="shoes" <?php echo ($category === 'shoes') ? 'selected' : ''; ?>>Shoes</option>
                    <option value="accessories" <?php echo ($category === 'accessories') ? 'selected' : ''; ?>>Accessories</option>
                </select>
                
                <select name="sort">
                    <option value="price_asc" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="name_asc" <?php echo ($sort === 'name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                    <option value="name_desc" <?php echo ($sort === 'name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
                </select>
                
                <button type="submit" class="btn">Apply Filters</button>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card animate__animated animate__fadeInUp">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>">
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>$<?php echo number_format($product['price'], 2); ?></p>
                        <form action="../user/add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="quantity" required>
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products animate__animated animate__fadeIn">No products found matching your criteria.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
