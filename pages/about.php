<?php
session_start();
include __DIR__ . '/../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - K I R A Fashion</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/about.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="about-container">
        <div class="about-header">
            <h1>About K I R A Fashion</h1>
            <p>Discover the story behind our passion for style and innovation</p>
        </div>

        <div class="about-content">
            <div class="about-card">
                <div class="about-card-icon">👗</div>
                <h3>Our Mission</h3>
                <p>To empower individuals to express their unique style through high-quality, sustainable fashion choices.</p>
            </div>

            <div class="about-card">
                <div class="about-card-icon">🌍</div>
                <h3>Sustainability</h3>
                <p>We're committed to eco-friendly practices and ethical sourcing in all our products.</p>
            </div>

            <div class="about-card">
                <div class="about-card-icon">💡</div>
                <h3>Innovation</h3>
                <p>Constantly pushing boundaries to bring you the latest in fashion technology and design.</p>
            </div>
        </div>

        <section class="team-section">
            <h2>Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="../assets/images/rajkumar.jpg" alt="Rajkumar Das">
                    <h4>Rajkumar Das</h4>
                    <p>Creative Director</p>
                    <p>Head of Design</p>
                </div>
                <div class="team-member">
                    <img src="../assets/images/sayan.jpg" alt="Sayan Basani">
                    <h4>Sayan Basani</h4>
                    <p>Co Creater</p>
                </div>
                <div class="team-member">
                    <img src="../assets/images/brothers.jpg" alt="Brothers">

                    <h4>My Team</h4>
                    <p>Marketing Managers</p>
                    <p>Supporter</p>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
