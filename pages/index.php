<?php
session_start();
include __DIR__ . '/../includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K I R A - Fashion</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1>Discover the Latest Fashion Trends</h1>
            <p>Explore our new collection and find your style.</p>
            <a href="collections.php" class="btn">Shop Now</a>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="feature">
                <img src="../assets/images/iphone 14.jpg" alt="Feature 1">
                <h2>New Arrivals</h2>
                <p>Stay ahead with our latest fashion pieces.</p>
            </div>
            
            <div class="feature">
                <img src="../assets/images/MacBook Pro.jpg" alt="Feature 2">
                <h2>Exclusive Designs</h2>
                <p>Unique styles crafted just for you.</p>
            </div>
            
            <div class="feature">
                <img src="../assets/images/Nike Air Max.jpg" alt="Feature 3">
                <h2>Quality Materials</h2>
                <p>Experience the best in comfort and durability.</p>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
