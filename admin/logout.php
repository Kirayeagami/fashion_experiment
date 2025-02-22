<?php
session_start();
require 'AdminLogger.php';

// Log the logout activity
if (isset($_SESSION['admin_logged_in'])) {
    AdminLogger::log($_SESSION['user_id'], 'Logout', 'Admin logged out');
}

session_destroy();
header("Location: login.php");
exit();
?>
