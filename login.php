<?php
session_start(); // Start a session

include('db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Successful login, start session and store user data
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            
            // Redirect to the dashboard or home page
            header("Location: dashboard.php");
            exit(); // Make sure to call exit() to stop further execution of the script
        } else {
            $message = "Invalid password!";
            $message_class = "message";
        }
    } else {
        $message = "User not found!";
        $message_class = "message";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
 html, body {
    font-family: 'Roboto', sans-serif;
    height: 100%;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f8f9fa; /* Optional background color */
}

.container {
    max-width: 600px;
    width: 100%;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: center; /* Vertically center the inputs inside the container */
    align-items: center; /* Horizontally center the inputs inside the container */
}

h2 {
    text-align: center;
    color: #34495e;
    margin-bottom: 30px;
    width: 100%; /* Ensures the heading spans the container width */
}

input[type="text"], input[type="password"] {
    width: 94%; /* Make all inputs the same width */
    padding: 15px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f2f3f5;
    font-size: 16px;
}

button {
    width: 100%; /* Make the button match the input width */
    padding: 15px;
    background-color: #2c3e50;
    color: #ecf0f1;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 20px;
}

button:hover {
    background-color: #34495e;
}

a {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #2c3e50;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.message {
    color: #e74c3c;
    text-align: center;
    margin-top: 20px;
}

.success {
    color: #2ecc71;
    text-align: center;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .container {
        width: 90%;
        padding: 15px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        
        <?php if (isset($_GET['message'])) { echo "<p class='success'>" . $_GET['message'] . "</p>"; } ?>
        
        <?php if (isset($message)) { echo "<p class='$message_class'>$message</p>"; } ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>
