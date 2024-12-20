<?php
session_start();
include 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

// Check if the user exists
if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
} else {
    // Redirect to the login page if no user found
    header("Location: login.php");
    exit();
}

// Handle form submission to update user settings
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : $user['username'];  // Use current name if not set
    $email = isset($_POST['email']) ? $_POST['email'] : $user['email']; // Use current email if not set
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // If no password, keep it empty

    // Validate password update
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $update_sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
    } else {
        $update_sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $username, $email, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Settings updated successfully!";
        header("Location: settings.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update settings. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        /* Global styles */
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
            background-color: #f8f9f9;
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

        /* Success/Error messages */
        .success,
        .error {
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #4CAF50;
        }

        .error {
            background-color: #f44336;
        }

        /* Form styles */
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        form label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #555;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Link styles */
        a {
            color: #4CAF50;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">YourSite</div>
        <div class="navbar-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="settings.php" class="active">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="sidebar">
        <a href="dashboard.php">Home</a>
        <a href="settings.php" class="active">Settings</a>
        <a href="reports.php">Reports</a>
        <a href="profile.php">Profile</a>
    </div>

    <div class="content">
        <h2>Update Your Settings</h2>

        <!-- Display success or error message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?php echo $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Settings form -->
        <form action="settings.php" method="POST">
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

            <label for="password">New Password (Leave empty to keep current password):</label>
            <input type="password" id="password" name="password"><br><br>

            <input type="submit" value="Update Settings">
        </form>

        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
