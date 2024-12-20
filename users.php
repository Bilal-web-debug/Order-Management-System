<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    
       <style>
        /* General Reset */
        

        /* Body */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
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
        .navbar .dash{
            font-size: 24px;
            font-weight: bold;

        }

        .navbar .logout{
            transform: translateX(-20px);
        }


        .navbar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition:  0.3s;
        }

        .navbar a:hover {
            background-color: #34495e;
        }

        /* Sidebar */
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

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
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

        tr:hover {
            background-color: #f8f9fa;
        }

        /* Action Buttons */
        .action-btn {
            background-color: #4CAF50;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .action-btn.delete {
            background-color: #f44336;
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
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <a class="dash" href="dashboard.php"> Dashboard</a>
        </div>
        <a href="logout.php" class="logout">Logout</a>
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

    Main Content
    <div class="content">
        <h2>Users List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>john.doe@example.com</td>
                    <td>Admin</td>
                    <td>
                        <a href="#" class="action-btn">Edit</a>
                        <a href="#" class="action-btn delete">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>jane.smith@example.com</td>
                    <td>Customer</td>
                    <td>
                        <a href="#" class="action-btn">Edit</a>
                        <a href="#" class="action-btn delete">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Emily Johnson</td>
                    <td>emily.johnson@example.com</td>
                    <td>Customer</td>
                    <td>
                        <a href="#" class="action-btn">Edit</a>
                        <a href="#" class="action-btn delete">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
