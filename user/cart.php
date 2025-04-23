<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!class_exists('Cart')) {
    class Cart {
        private $conn; // Database connection
        private $items = []; // Cart items

        public function __construct($dbConnection) {
            $this->conn = $dbConnection; // Assign the database connection
            if (isset($_SESSION['cart'])) {
                $this->items = $_SESSION['cart'];
            } else {
                $_SESSION['cart'] = [];
            }
        }

        public function addItem($product_id, $name, $price, $quantity = 1) {
            $product_id = intval($product_id);
            $quantity = intval($quantity);

            if ($quantity <= 0) {
                throw new InvalidArgumentException("Quantity must be greater than 0");
            }

            if (isset($this->items[$product_id])) {
                $this->items[$product_id]['quantity'] += $quantity;
            } else {
                $this->items[$product_id] = [
                    'product_id' => $product_id,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity
                ];
            }

            $this->save();
        }

        public function updateItem($product_id, $quantity) {
            $product_id = intval($product_id);
            $quantity = intval($quantity);
            $user_id = $_SESSION['user_id'];

            if ($quantity <= 0) {
                $this->removeItem($product_id);
                return;
            }

            if (isset($this->items[$product_id])) {
                $this->items[$product_id]['quantity'] = $quantity;
                $this->save();

                $stmt = $this->conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                
                error_log("Attempting to update product $product_id to quantity $quantity");
                if ($stmt->execute()) {
                    error_log("Successfully updated product $product_id to quantity $quantity in database.");
                    error_log("Current cart items: " . print_r($this->items, true));
                } else {
                    error_log("Failed to update product $product_id in database: " . $stmt->error);
                }
            } else {
                error_log("Failed to update product $product_id: Product not found in cart");
            }
        }

        public function removeItem($product_id) {
            error_log("Attempting to remove product with ID: $product_id");

            $product_id = intval($product_id);
            $user_id = $_SESSION['user_id'];

            if (isset($this->items[$product_id])) {
                $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $user_id, $product_id);
                
                if ($stmt->execute()) {
                    unset($this->items[$product_id]);
                    $this->save();
                    error_log("Successfully removed product $product_id from cart in database.");
                    error_log("Current cart items: " . print_r($this->items, true));
                } else {
                    error_log("Failed to remove product $product_id from database: " . $stmt->error);
                }
            } else {
                error_log("Failed to remove product $product_id: Product not found in cart");
            }
        }

        public function clearCart() {
            $user_id = $_SESSION['user_id'];
            $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $this->items = [];
                $this->save();
                error_log("Successfully cleared all items from the cart.");
            } else {
                error_log("Failed to clear cart: " . $stmt->error);
            }
        }

        public function getItems() {
            return $this->items;
        }

        public function getTotal() {
            $total = 0;
            foreach ($this->items as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            return $total;
        }

        private function save() {
            $_SESSION['cart'] = $this->items;
        }
    }
}

// Initialize cart
$cart = new Cart($conn);

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data received: " . print_r($_POST, true)); // Debugging log

    try {
        if (isset($_POST['remove']) && !empty($_POST['remove'])) {
            $product_id = intval($_POST['remove']);
            error_log("Removing product ID: $product_id");
            if (isset($cart->getItems()[$product_id])) {
                $cart->removeItem($product_id);
            } else {
                error_log("Failed to remove product $product_id: Product not found in cart");
            }
        } elseif (isset($_POST['update'])) {
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);
            error_log("Updating product ID: $product_id with quantity: $quantity");
            $cart->updateItem($product_id, $quantity);
        } elseif (isset($_POST['clear'])) {
            error_log("Clearing all items from the cart.");
            $cart->clearCart();
        }
        // Redirect to avoid form resubmission and refresh the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        error_log("Cart error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="../assets/css/cart.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="main-content" style="padding-top: 120px;">
        <h2>Your Cart</h2>

        <?php $items = $cart->getItems(); ?>
        <?php if (!empty($items)): ?> 

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>
                                <form action="" method="post" class="update-form">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                    <button type="submit" name="update" class="update-btn">Update</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <form action="" method="post" class="remove-form">
                                    <input type="hidden" name="remove" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form action="" method="post" class="clear-cart-form">
            <button type="submit" name="clear" class="clear-cart-btn">Clear All Items</button>
        </form>

            <div class="total">
                <p><strong>Total:</strong> $<?php echo number_format($cart->getTotal(), 2); ?></p>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <p>Your cart is empty!</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
