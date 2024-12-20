<?php
session_start();
include 'db_config.php';

// Ensure user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get the order ID from URL
$order_id = $_GET['id'];

// Fetch the order details
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Handle form submission for status update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    
    // Update the order status
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        // Send email notification to the user
        $user_id = $order['user_id'];
        $sql = "SELECT email FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $to = $user['email'];
        $subject = "Your Order Status has been Updated";
        $message = "Hello, your order (ID: $order_id) status has been updated to $status.";
        $headers = "From: no-reply@yourstore.com";

        // Send the email
        mail($to, $subject, $message, $headers);

        $_SESSION['success'] = "Order status updated and email sent!";
        header("Location: admin_dashboard.php");
    } else {
        $_SESSION['error'] = "Failed to update order status. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
</head>
<body>
    <h2>Edit Order</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
    <?php endif; ?>

    <form action="edit_order.php?id=<?php echo $order_id; ?>" method="POST">
        <label for="status">Order Status</label>
        <select name="status" required>
            <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
            <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
            <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
        </select><br>

        <button type="submit">Update Status</button>
    </form>
</body>
</html>
