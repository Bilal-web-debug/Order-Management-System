<?php
session_start();
include 'db_config.php';

// Ensure user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get the product ID from URL
$product_id = $_GET['id'];

// Fetch the product details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Handle form submission for product update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    // Move uploaded image
    if (!empty($image)) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image_update = ", image = ?";
    } else {
        $image_update = "";
    }

    // Update product details
    $sql = "UPDATE products SET name = ?, description = ?, price = ? $image_update WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!empty($image)) {
        $stmt->bind_param("ssisi", $name, $description, $price, $target_file, $product_id);
    } else {
        $stmt->bind_param("ssii", $name, $description, $price, $product_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product updated successfully!";
        header("Location: admin_dashboard.php");
    } else {
        $_SESSION['error'] = "Failed to update product. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
</head>
<body>
    <h2>Edit Product</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
    <?php endif; ?>

    <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>

        <label for="description">Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br>

        <label for="price">Price</label>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" required><br>

        <label for="image">Image</label>
        <input type="file" name="image"><br>

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
