<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update password only if it's entered
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssi", $username, $email, $password, $user_id);
    } else {
        $update_query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $username, $email, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: dashboard.php");
    } else {
        $_SESSION['error'] = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        /* General Reset */
        *  body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9
    
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

        /* Form Container */
        form {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 100%;
    max-width: 400px;
    margin: 150px auto; 
}

        /* Title */
        h2 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Error Message */
        p {
            color: red;
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* Form Inputs */
        form label {
            font-size: 16px;
            color: #34495e;
            display: block;
            margin-bottom: 8px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
        }

        form input[type="text"]:focus,
        form input[type="email"]:focus,
        form input[type="password"]:focus {
            outline: none;
            border-color: #1e90ff;
            box-shadow: 0 0 5px rgba(30, 144, 255, 0.5);
        }

        /* Submit Button */
        form button {
            background-color: #1e90ff;
            color: #fff;
            font-size: 16px;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #007bff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a class="dash" href="dashboard.php">Dashboard</a>
        <a  class="logout" href="logout.php">Logout</a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php" c>Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="update_profile.php">Update Profile</a>
        <!-- <a href="settings.php">Settings</a> -->
    </div>


    
    <form action="update_profile.php" method="POST">
        <h2>Update Profile</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php endif; ?>
        <label for="username">Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required><br>

        <label for="email">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required><br>

        <label for="password">New Password (Leave empty if not changing)</label>
        <input type="password" name="password"><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
