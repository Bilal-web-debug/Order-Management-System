<?php
// Start the session
session_start();

// Include the database connection file
include('db_config.php'); // Ensure this path is correct

// Fetch orders from the database
$sql = "SELECT * FROM orders"; // Modify this query as needed to get orders from your database
$order_result = $conn->query($sql);

// Check if the query returned results
if ($order_result === false) {
    $_SESSION['error'] = "Error fetching orders: " . $conn->error;
    header('Location: orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <style>
        /* General Reset */
     

        /* Body */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar .logout {
            transform: translateX(-20px); 
            

        }

        .navbar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background-color: #34495e;
        }

        .sidebar {
            width: 220px;
            background-color: #34495e;
            position: fixed;
            top: 60px;
            left: 0;
            height: calc(100% - 60px);
            padding: 20px 0;
        }

        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            font-size: 16px;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #2c3e50;
            border-left: 3px solid #e74c3c;
           
        }

        

        .content {
            margin-left: 240px;
            padding: 80px 20px;
        }
        


        /* Content */
        .content {
            margin-left: 240px;
            padding: 80px 20px;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
        }

        /* Success/Error Messages */
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        p.success {
            color: green;
        }

        p.error {
            color: red;
        }

        /* Table */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f3f5;
            color: #34495e;
            font-size: 16px;
        }

        td {
            font-size: 14px;
        }

        .delete-btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #e74c3c; /* Red color */
        color: #fff; /* White text */
        text-decoration: none;
        border-radius: 5px; /* Rounded corners */
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

        tr:hover {
            background-color: #f8f9fa;
        }

        select {
            padding: 6px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            cursor: pointer;
        }

        select:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        a {
            color: #e74c3c;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }

            .content {
                padding: 20px;
            }

            table {
                width: 100%;
                margin: 0;
            }

            th, td {
                font-size: 12px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
        
    
    </div>
    <div class="navbar">
        <div class="logo">Order Management</div>
        <a class="logout" href="logout.php">Logout</a>
       
    </div>

       <!-- Sidebar -->
       <div class="sidebar">
        <a href="dashboard.php" >Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="update_profile.php">Update Profile</a>
        <!-- <a href="settings.php">Settings</a> -->
    </div>


    <div class="content">
        <h2>Manage Orders</h2>

        <!-- Success/Error Message Display -->
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Order List -->
        <table>
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
                <?php if ($order_result->num_rows > 0): ?>
                    <?php while ($order = $order_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['user_id']; ?></td>
                        <td><?php echo isset($order['total_price']) ? "$" . number_format($order['total_price'], 2) : '$0.00'; ?></td>
                        <td><?php echo isset($order['shipping_address']) ? htmlspecialchars($order['shipping_address']) : 'N/A'; ?></td>
                        <td>
                            <form action="edit_order.php?id=<?php echo $order['id']; ?>" method="POST">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </form>
                        </td>
                        <td>
    <a href="delete_order.php?id=<?php echo $order['id']; ?>" 
       onclick="return confirm('Are you sure you want to delete this order?')" 
       class="delete-btn">Delete</a>
</td>

                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>
