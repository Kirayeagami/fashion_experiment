<?php
session_start();
include __DIR__ . '/../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - K I R A Fashion</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/privacy.css">

</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="privacy-container">
        <div class="privacy-header">
            <h1>Privacy Policy</h1>
            <p>Your privacy is important to us. This policy explains how we handle your information.</p>
        </div>

        <div class="privacy-content">
            <div class="privacy-section">
                <h2>Information Collection</h2>
                <p>We collect information when you register on our site, place an order, subscribe to our newsletter, or fill out a form. This may include your name, email address, mailing address, phone number, or credit card information.</p>
            </div>

            <div class="privacy-section">
                <h2>Use of Information</h2>
                <p>The information we collect may be used to personalize your experience, improve our website, improve customer service, process transactions, send periodic emails, and administer contests, promotions, or surveys.</p>
            </div>

            <div class="privacy-section">
                <h2>Information Protection</h2>
                <p>We implement a variety of security measures to maintain the safety of your personal information. We use secure servers and all supplied sensitive/credit information is transmitted via Secure Socket Layer (SSL) technology.</p>
            </div>

            <div class="privacy-section">
                <h2>Cookies</h2>
                <p>We use cookies to understand and save your preferences for future visits and compile aggregate data about site traffic and site interaction so that we can offer better site experiences and tools in the future.</p>
            </div>

            <div class="privacy-section">
                <h2>Third-Party Disclosure</h2>
                <p>We do not sell, trade, or otherwise transfer to outside parties your personally identifiable information unless we provide users with advance notice. This does not include website hosting partners and other parties who assist us in operating our website, conducting our business, or serving our users.</p>
            </div>

            <div class="privacy-section">
                <h2>Your Consent</h2>
                <p>By using our site, you consent to our privacy policy. If we decide to change our privacy policy, we will post those changes on this page.</p>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
