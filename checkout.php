<?php
session_start();
include 'db_config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect to cart if the cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $total_price = 0;
    $order_items = [];

    // Calculate total and prepare order details
    foreach ($_SESSION['cart'] as $product_id => $cart_item) {
        $total_price += $cart_item['price'] * $cart_item['quantity'];
        $order_items[] = "{$cart_item['name']} (x{$cart_item['quantity']})";
    }

    $shipping_address = $_POST['shipping_address'];
    
    // Insert order into the database
    $sql = "INSERT INTO orders (user_id, total_price, shipping_address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $user_id, $total_price, $shipping_address);
    
    if ($stmt->execute()) {
        // Get the last inserted order ID
        $order_id = $stmt->insert_id;

        // Insert order items into the order_items table
        foreach ($order_items as $item) {
            $sql = "INSERT INTO order_items (order_id, product_name) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $order_id, $item);
            $stmt->execute();
        }

        // Clear the cart
        unset($_SESSION['cart']);
        
        $_SESSION['success'] = "Your order has been placed successfully!";
        header("Location: order_confirmation.php");
    } else {
        $_SESSION['error'] = "Failed to place your order. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h2>Checkout</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
    <?php endif; ?>
    
    <form action="checkout.php" method="POST">
        <label for="shipping_address">Shipping Address</label>
        <textarea name="shipping_address" required></textarea><br>
        
        <button type="submit">Place Order</button>
    </form>
    
    <p>Total: $<?php echo number_format($total_price, 2); ?></p>
</body>
</html>
