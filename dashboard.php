<?php
session_start(); // Start the session to access user data

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
include('db_config.php');

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    header("Location: login.php");
    exit();
}

// Fetch dashboard statistics
$sql_products = "SELECT COUNT(*) AS total_products FROM products";
$sql_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$sql_revenue = "SELECT SUM(total_amount) AS total_revenue FROM orders";
$sql_users = "SELECT COUNT(*) AS total_users FROM users";

$total_products = $conn->query($sql_products)->fetch_assoc()['total_products'];
$total_orders = $conn->query($sql_orders)->fetch_assoc()['total_orders'];
$total_revenue = $conn->query($sql_revenue)->fetch_assoc()['total_revenue'];
$total_users = $conn->query($sql_users)->fetch_assoc()['total_users'];

// Fetch recent orders
$sql_recent_orders = "SELECT id, total_amount, status FROM orders ORDER BY created_at DESC LIMIT 5";
$recent_orders = $conn->query($sql_recent_orders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Dashboard</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

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

        .navbar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .navbar .logout {
            transform: translateX(-20px); 
            

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

        .dashboard-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s;
            margin-bottom: 20px;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-card h3 {
            color: #34495e;
            margin-bottom: 10px;
        }

        .dashboard-card p {
            font-size: 28px;
            color: #e74c3c;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f3f5;
            color: #34495e;
        }

        table tr:hover {
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }

            .content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">E-commerce Dashboard</div>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="active">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="update_profile.php">Update Profile</a>
        <!-- <a href="settings.php">Settings</a> -->
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Welcome, <?php echo $row['username']; ?>!</h2>
        <div class="dashboard-overview">
            <div class="dashboard-card">
                <h3>Total Products</h3>
                <p><?php echo $total_products; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Total Orders</h3>
                <p><?php echo $total_orders; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($total_revenue, 2); ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="dashboard-card" >
            <h3>Recent Orders</h3>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo $order['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
