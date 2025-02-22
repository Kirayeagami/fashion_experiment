<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if user is logged in as a seller
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "seller") {
    header("Location: ../user/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../assets/css/order_page.css">
    <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="order-container">
        <h2>Confirm Your Order</h2>
        <button id="placeOrderBtn">Place Order</button>
        <p id="orderMessage"></p>
    </div>

    <script>
        document.getElementById("placeOrderBtn").addEventListener("click", function() {
            fetch("order_script.php", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: "order=true"
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                document.getElementById("orderMessage").innerHTML = data;
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById("orderMessage").innerHTML = "An error occurred while processing your order.";
            });
        });
    </script>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
