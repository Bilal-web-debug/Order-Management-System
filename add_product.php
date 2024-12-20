<?php
session_start();
include 'db_config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    // Move uploaded image
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    // Insert product into the database
    $sql = "INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $name, $description, $price, $target_file);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product added successfully!";
        header("Location: admin_dashboard.php");
    } else {
        $_SESSION['error'] = "Failed to add product. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>
<body>
    <h2>Add New Product</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
    <?php endif; ?>
    
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" name="name" required><br>

        <label for="description">Description</label>
        <textarea name="description" required></textarea><br>

        <label for="price">Price</label>
        <input type="number" name="price" step="0.01" required><br>

        <label for="image">Image</label>
        <input type="file" name="image" required><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
