<?php
include 'db.php';

// Fetch services from the database
$sql = "SELECT * FROM services";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- Services Section -->
    <section class="services">
        <h2>Our Services</h2>
        <div class="service-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="service-card">
                    <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

</body>
</html>
