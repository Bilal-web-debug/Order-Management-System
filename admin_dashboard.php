<?php
session_start();
include 'db_config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete product functionality
if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];
    
    // Delete the product
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product deleted successfully!";
        header("Location: admin_dashboard.php");
    } else {
        $_SESSION['error'] = "Failed to delete product. Please try again.";
    }
}

// Fetch all users
$sql = "SELECT * FROM users";
$user_result = $conn->query($sql);

// Fetch all orders
$sql = "SELECT * FROM orders";
$order_result = $conn->query($sql);

// Fetch all products
$sql = "SELECT * FROM products";
$product_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    
    <!-- Success/Error Message Display -->
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Manage Users Section -->
    <h3>Manage Users</h3>
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $user_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['role']; ?></td>
                <td><a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> | <a href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Manage Orders Section -->
    <h3>Manage Orders</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Total Price</th>
                <th>Shipping Address</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $order_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['user_id']; ?></td>
                <td><?php echo "$" . number_format($order['total_price'], 2); ?></td>
                <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                <td>
                    <form action="edit_order.php?id=<?php echo $order['id']; ?>" method="POST">
                        <select name="status" onchange="this.form.submit()">
                            <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                        </select>
                    </form>
                </td>
                <td><a href="delete_order.php?id=<?php echo $order['id']; ?>" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Manage Products Section -->
    <h3>Manage Products</h3>
    <a href="add_product.php">Add New Product</a>
    <table border="1">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $product_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo "$" . number_format($product['price'], 2); ?></td>
                <td><a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> | <a href="?delete_product=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
