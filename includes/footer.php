<?php
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<footer class="modern-footer">

    <div class="footer-container">
        <div class="footer-brand">
            <h3 class="footer-logo">K I R A</h3>
            <p class="footer-tagline">Your Fashion Destination</p>
            <p class="footer-description">Explore the latest trends and styles with us.</p>
            <p class="footer-address">123 Fashion St, Style City, SC 12345</p>
        </div>

        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="../pages/index.php">Home</a></li>
                <li><a href="../pages/collections.php">Collections</a></li>
                <li><a href="../pages/contact.php">Contact</a></li>
                <li><a href="../admin/login.php">Login</a></li>
            </ul>
        </div>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <div class="footer-social">
            <h4>Follow Us</h4>
            <div class="social-icons">
                <a href="https://www.facebook.com/share/152fSHc5z7/" class="social-icon" title="Facebook">F
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.twitter.com/@KiraYeagami" class="social-icon" title="Twitter">T
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.instagram.com/kira_yeagami?igsh=OGQ5ZDc2ODk2ZA==" class="social-icon" title="Instagram">I
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://linkedin.com/in/rajkumar-das-7391a7313" class="social-icon" title="LinkedIn">L
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <div class="footer-newsletter">
            <h4>Newsletter</h4>
            <?php if (isset($_SESSION['newsletter_message'])): ?>
                <div class="newsletter-message success"><?php echo $_SESSION['newsletter_message']; ?></div>
                <?php unset($_SESSION['newsletter_message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['newsletter_error'])): ?>
                <div class="newsletter-message error"><?php echo $_SESSION['newsletter_error']; ?></div>
                <?php unset($_SESSION['newsletter_error']); ?>
            <?php endif; ?>
            <form class="newsletter-form" action="../includes/newsletter.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit">
                    <i class="fas fa-paper-plane"></i>Subscribe
                </button>
                <p class="privacy-notice">
                    By subscribing, you agree to our <a href="../pages/privacy.php">Privacy Policy</a>.
                </p>
            </form>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> K I R A Fashion. All rights reserved.</p>
    </div>
</footer>
