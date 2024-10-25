<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = $conn->real_escape_string($_POST['category_id']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $seller_id = $_SESSION['user_id']; 

    $target_dir = "uploads/";
    $image = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (seller_id, name, category_id, price, description, image)
                VALUES ('$seller_id', '$name', '$category_id', '$price', '$description', '$image')";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='success'>Product added successfully! <a href='seller.php'>Go to your Dashboard</a></div>";
        } else {
            echo "<div class='error'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    } else {
        echo "<div class='error'>Error uploading image!</div>";
    }
}

$sql = "SELECT * FROM categories";
$categories = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - GreenTail Pets</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="POST" action="add_product.php" enctype="multipart/form-data">
        <h2>Add Product</h2>
        <label>Product Name:</label><input type="text" name="name" required>
        <label>Category:</label>
        <select name="category_id">
            <?php while ($category = $categories->fetch_assoc()) { ?>
                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
            <?php } ?>
        </select>
        <label>Price:</label><input type="text" name="price" required>
        <label>Description:</label><textarea name="description" required></textarea>
        <label>Product Image:</label><input type="file" name="image" required>
        <div class="button-container">
            <button type="submit" class="btn">Add Product</button>
            <a href="seller.php" class="btn back-btn">Back to Dashboard</a>
        </div>
    </form>
</body>
</html>
