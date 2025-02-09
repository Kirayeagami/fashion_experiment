<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Landing Page</title>
    <link rel="stylesheet" href="css/index.css">
    <script defer src="script.js"></script> <!-- Link to JavaScript -->
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">K I R A</a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="collections.php">Collections</a></li>
                    <li><a href="http://localhost/e-comm/index.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    
                    <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "seller"): ?>
                        <!-- Seller-specific links -->
                        <li><a href="add_product.php">Add Product</a></li>
                        <li><a href="seller_dashboard.php">Dashboard</a></li>
                        <li><a href="order_details.php">View Orders</a></li>
                    <?php endif; ?>
                    
                    <li><a href="profile.php">Profile</a></li>
                </ul>
            </nav>
            <button id="theme-toggle" class="theme-toggle">🌙</button> <!-- Dark mode button -->
            <?php if (isset($_SESSION["user_name"])): ?>                   

                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION["user_name"]); ?>)</a></li>
                    <!-- Hide seller-specific links for customers -->
                    <?php if (!empty($_SESSION["user_role"]) && $_SESSION["user_role"] === "seller"): ?>
                        <!-- Optionally, add something specific for sellers -->
                    <?php endif; ?>

                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Discover the Latest Fashion Trends</h1>
            <p>Explore our new collection and find your style.</p>
            <a href="collections.php" class="btn">Shop Now</a>
        </div>
    </section>

    <section class="features">
    <div class="container">
        <!-- <a href="collections.php" class="feature-link"> -->
            <div class="feature">
                <img src="images/iphone 14.jpg" alt="Feature 1">
                <h2>New Arrivals</h2>
                <p>Stay ahead with our latest fashion pieces.</p>
            </div>
        </a>
        
        <!-- <a href="collections.php" class="feature-link"> -->
            <div class="feature">
                <img src="images/MacBook Pro.jpg" alt="Feature 2">
                <h2>Exclusive Designs</h2>
                <p>Unique styles crafted just for you.</p>
            </div>
        </a>
        
        <!-- <a href="collections.php" class="feature-link"> -->
            <div class="feature">
                <img src="images/Nike Air Max.jpg" alt="Feature 3">
                <h2>Quality Materials</h2>
                <p>Experience the best in comfort and durability.</p>
            </div>
        </a>
    </div>
</section>

    <footer>
        <div class="socials">
            <section id="socials" class="animated">
                <center>
                    <h3>Follow Us</h3>
                </center>
                <div class="social-icons">
                    <a href="https://www.facebook.com/share/152fSHc5z7/" target="_blank">
                        <img src="images/rajkumar.jpg" alt="Facebook">
                    </a>
                    <br>
                    <a href="https://www.facebook.com/share/182Y69wydL/" target="_blank">
                        <img src="images/sayan.jpg" alt="Facebook">
                    </a>
                    <br><br>

                    <a href="https://www.instagram.com/kira_yeagami?igsh=OGQ5ZDc2ODk2ZA==" target="_blank">
                        <img src="images/rajkumar.jpg" alt="Instagram">
                    </a>
                    <br>
                    <a href="https://www.instagram.com/sayanbasani?igsh=MzRlODBiNWFlZA==" target="_blank">
                        <img src="images/sayan.jpg" alt="Instagram">
                    </a>

                    <a href="https://twitter.com" target="_blank">
                        <img src="images/twitter.png" alt="Twitter">
                    </a>

                    <a href="https://www.linkedin.com" target="_blank">
                        <img src="images/linkedin.png" alt="LinkedIn">
                    </a>
                </div>
            </section>    
        </div>

        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> K I R A. Fashion Website by Us.</p>
        </div>
    </footer>
</body>
</html>
