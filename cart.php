<?php
session_start();
include 'db_config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get cart items from session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding product to cart
if (isset($_GET['add_to_cart'])) {
    $product_id = $_GET['add_to_cart'];
    
    // Check if product already exists in cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity']++;
    } else {
        // Fetch product details
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        // Add product to cart session
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1
        ];
    }

    header("Location: cart.php");
}

// Handle removing product from cart
if (isset($_GET['remove_from_cart'])) {
    $product_id = $_GET['remove_from_cart'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
}

// Calculate total
$total_price = 0;
foreach ($_SESSION['cart'] as $product_id => $cart_item) {
    $total_price += $cart_item['price'] * $cart_item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
</head>
<body>
    <h2>Your Shopping Cart</h2>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty!</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $product_id => $cart_item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cart_item['name']); ?></td>
                    <td><?php echo "$" . number_format($cart_item['price'], 2); ?></td>
                    <td><?php echo $cart_item['quantity']; ?></td>
                    <td><?php echo "$" . number_format($cart_item['price'] * $cart_item['quantity'], 2); ?></td>
                    <td>
                        <a href="cart.php?remove_from_cart=<?php echo $product_id; ?>">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p>Total: $<?php echo number_format($total_price, 2); ?></p>
        <a href="checkout.php">Proceed to Checkout</a>
    <?php endif; ?>
</body>
</html>
