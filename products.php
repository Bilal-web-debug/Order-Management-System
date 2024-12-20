<?php
session_start();
include 'db_config.php';

// Fetch all products
$sql = "SELECT * FROM products";
$product_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
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

        .navbar a:hover {
            background-color: #34495e;
        }

        .navbar .logout {
            transform: translateX(-20px); 
            

        }

        .sidebar {
            width: 220px;
            background-color: #34495e;
            position:absolute;
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
        /* Basic button style */
.button {
    display: inline-block;
    padding: 10px 15px;
    margin: 5px;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    border: 1px solid #ccc;
    transition: background-color 0.3s;
}

/* Edit button style */
.edit-btn {
    background-color: #4CAF50; /* Green */
    color: white;
}

.edit-btn:hover {
    background-color: #45a049; /* Darker green */
}

/* Delete button style */
.delete-btn {
    background-color: #f44336; /* Red */
    color: white;
}

.delete-btn:hover {
    background-color: #e53935; /* Darker red */
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
        <div class="logo">Admin Panel</div>
        <a  class= "logout" href="logout.php">Logout</a>
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

    <!-- Content -->
    <div class="content">
        <h2>Manage Products</h2>

        <!-- Success/Error Message Display -->
        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Add Product Button -->
        <a href="add_product.php" style="display: inline-block; margin-bottom: 20px; padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; font-size: 16px; border-radius: 5px; transition: background-color 0.3s ease;">Add New Product</a>

        <!-- Product List -->
        <table>
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
                    <td>
    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="button edit-btn">Edit</a> | 
    <a href="admin_dashboard.php?delete_product=<?php echo $product['id']; ?>" 
       onclick="return confirm('Are you sure you want to delete this product?')" class="button delete-btn">Delete</a>
</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
