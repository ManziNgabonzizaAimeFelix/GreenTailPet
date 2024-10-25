<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$displayName = htmlspecialchars(explode('@', $username)[0]);

$sqlProducts = "SELECT id, name AS product_name, description AS product_description, price AS product_price, image FROM products";
$productsResult = $conn->query($sqlProducts);

// Get buyer's orders
$buyer_id = $_SESSION['user_id'];
$sqlOrders = "SELECT o.id, o.order_date, o.status, o.rejection_reason, p.name AS product_name
              FROM orders o
              JOIN products p ON o.product_id = p.id
              WHERE o.buyer_id = $buyer_id";
$ordersResult = $conn->query($sqlOrders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenTail Pets - Buyer</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f0f8ff; color: #333; font-family: Arial, sans-serif; }
        nav { display: flex; align-items: center; justify-content: space-between; padding: 20px; background-color: rgba(0, 0, 0, 0.7); }
        nav img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 20px; }
        h1 { text-align: center; margin: 20px; color: #2c3e50; }
        .products, .orders { padding: 20px; }
        .product, .order { margin: 10px; padding: 15px; background-color: white; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); text-align: center; }
        footer { text-align: center; padding: 20px; background-color: rgba(0, 0, 0, 0.7); color: white; position: relative; bottom: 0; width: 100%; }
        .btn { background-color: #00b894; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; transition: background-color 0.3s; }
        .btn:hover { background-color: #009e7a; }
    </style>
</head>
<body>

<nav>
    <img src="logo.png" alt="GreenTail Pets Logo">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="buyer.php">Products</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<h1>Welcome, <?php echo htmlspecialchars($displayName); ?>!</h1>

<div class="products">
    <h2>Products:</h2>
    <?php if ($productsResult->num_rows > 0): ?>
        <?php while($row = $productsResult->fetch_assoc()): ?>
            <div class="product">
                <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                <p><?php echo htmlspecialchars($row['product_description']); ?></p>
                <p><strong>Price:</strong> Frw <?php echo number_format($row['product_price'], 2); ?></p>
                <a href="place_order.php?id=<?php echo $row['id']; ?>" class="btn"><i class="fas fa-shopping-cart"></i> Place Order</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No products available at the moment.</p>
    <?php endif; ?>
</div>

<div class="orders">
    <h2>Your Orders:</h2>
    <?php if ($ordersResult->num_rows > 0): ?>
        <?php while ($order = $ordersResult->fetch_assoc()): ?>
            <div class="order">
                <h3>Product: <?php echo htmlspecialchars($order['product_name']); ?></h3>
                <p>Order Date: <?php echo $order['order_date']; ?></p>
                <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                <?php if ($order['status'] === 'rejected'): ?>
                    <p>Rejection Reason: <?php echo htmlspecialchars($order['rejection_reason']); ?></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> GreenTail Pets. All rights reserved.</p>
    <p>Your trusted partner in enhancing agriculture.</p>
</footer>

<?php
$conn->close();
?>
</body>
</html>
