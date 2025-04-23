<?php
session_start();
include __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="shortcut icon" href="https://i.ibb.co/7NV0mNm9/IMG-20230803-013043224-transformed-x4-x16.jpg" type="image/png" />
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/contact.css">

</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="contact-container">
        <div class="contact-header">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you!</p>
        </div>
        <div class="contact-content">
            <form class="contact-form" action="../includes/process_contact.php" method="POST" onsubmit="return handleFormSubmit(event)">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
            <div class="contact-info">
                <h3>Our Information</h3>
                <div class="contact-info-item">
                    <div class="contact-info-icon">📍</div>
                    <div class="contact-info-content">
                        <h4>Address</h4>
                        <p>123 Fashion Street, Style City , Chittaranjan</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon">📞</div>
                    <div class="contact-info-content">
                        <h4>Phone</h4>
                        <p>+91 7632032805</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon">✉️</div>
                    <div class="contact-info-content">
                        <h4>Email</h4>
                        <p>info@fashionstore.com</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script>
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${type}`;
            alertDiv.textContent = message;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        function handleFormSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    form.reset();
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('An error occurred. Please try again.', 'error');
            });
        }
    </script>
</body>
</html>
