<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            text-align: center;
        }
        .order-container {
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            width: 50%;
        }
        button {
            background-color: #ff6600;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #e65c00;
        }
    </style>
</head>
<body>

    <div class="order-container">
        <h2>Confirm Your Order</h2>
        <button id="placeOrderBtn">Place Order</button>
        <p id="orderMessage"></p>
    </div>

    <script>
        document.getElementById("placeOrderBtn").addEventListener("click", function() {
            fetch("order_script.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "order=true"
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("orderMessage").innerHTML = data;
            })
            .catch(error => console.error("Error:", error));
        });
    </script>

</body>
</html>
