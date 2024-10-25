<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("Product ID not specified.");
}
$product_id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = '$product_id' AND seller_id = '" . $_SESSION['user_id'] . "'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = $conn->real_escape_string($_POST['category_id']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $sql = "UPDATE products SET name='$name', category_id='$category_id', price='$price', description='$description' WHERE id='$product_id'";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>Product updated successfully! <a href='seller.php'>Go back to your dashboard</a></div>";
    } else {
        echo "<div class='error'>Error updating product: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - GreenTail Pets</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>">
        <h2>Edit Product</h2>
        <label>Product Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        <label>Category:</label>
        <select name="category_id">
            <?php
            $categories = $conn->query("SELECT * FROM categories");
            while ($category = $categories->fetch_assoc()) {
                $selected = ($category['id'] == $product['category_id']) ? 'selected' : '';
                echo "<option value='{$category['id']}' $selected>{$category['category_name']}</option>";
            }
            ?>
        </select>
        <label>Price:</label>
        <input type="text" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        <button type="submit">Update Product</button>
        <a href="seller.php" class="back-btn">Back to Dashboard</a>
    </form>

    <style>
        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            background-color: #ff6347;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #e5533d; 
        }
    </style>
</body>
</html>
