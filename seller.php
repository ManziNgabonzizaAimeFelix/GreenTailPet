<?php
session_start();
include 'db.php';

if ($_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit();
}

$seller_id = $_SESSION['user_id'];
$sqlSeller = "SELECT username FROM users WHERE id = '$seller_id'";
$sellerResult = $conn->query($sqlSeller);
$seller = $sellerResult->fetch_assoc();

$sellerName = htmlspecialchars(explode('@', $seller['username'])[0]);

// Get the seller's products
$sqlProducts = "SELECT * FROM products WHERE seller_id = $seller_id";
$result = $conn->query($sqlProducts);

// Handle order approval/rejection
if (isset($_POST['action'])) {
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sqlUpdateOrder = "UPDATE orders SET status = 'approved' WHERE id = $orderId";
        $conn->query($sqlUpdateOrder);
    } elseif ($action === 'reject') {
        $reason = htmlspecialchars($_POST['reason']);
        $sqlUpdateOrder = "UPDATE orders SET status = 'rejected', rejection_reason = '$reason' WHERE id = $orderId";
        $conn->query($sqlUpdateOrder);
    }
}

// Get orders for the seller's products
$sqlOrders = "SELECT o.id, o.order_date, o.status, o.rejection_reason, p.name AS product_name, u.username AS buyer_username 
              FROM orders o
              JOIN products p ON o.product_id = p.id
              JOIN users u ON o.buyer_id = u.id
              WHERE p.seller_id = $seller_id";
$ordersResult = $conn->query($sqlOrders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Dashboard - GreenTail Pets</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f0f8ff; color: #333; font-family: Arial, sans-serif; text-align: center; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px; background-color: rgba(0, 0, 0, 0.7); }
        nav img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 20px; }
        h1 { margin: 20px; color: #2c3e50; }
        .orders, .products { margin-top: 20px; padding: 20px; }
        .order, .product { margin: 10px; padding: 15px; background-color: white; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        footer { text-align: center; padding: 20px; background-color: rgba(0, 0, 0, 0.7); color: white; position: relative; bottom: 0; width: 100%; }
        .btn { padding: 5px 10px; margin: 5px; }
        .btn-approve { background-color: #28a745; color: white; }
        .btn-reject { background-color: #dc3545; color: white; }
        .form-reason { display: none; }
    </style>
</head>
<body>

<nav>
    <img src="logo.png" alt="GreenTail Pets Logo">
    <ul>
        <li><a href="seller.php">Dashboard</a></li>
        <li><a href="add_product.php"><i class="fas fa-plus"></i> Product</a></li>
        <li><a href="logout.php"><i class="fas fa-door-open"></i> Logout</a></li>
    </ul>
</nav>

<h1>Welcome, <?php echo $sellerName; ?>! Here are your products and orders:</h1>

<div class="products">
    <h2>Your Products:</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($product = $result->fetch_assoc()) { ?>
            <div class="product">
                <img src="uploads/<?php echo $product['image']; ?>" alt="Product Image" style="max-width: 200px; height: auto;">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Price:</strong> Frw <?php echo number_format($product['price'], 2); ?></p>
                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="icon-link"><i class="fas fa-edit"></i> Edit</a>
                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="icon-link"><i class="fas fa-trash"></i> Delete</a>
            </div>
        <?php } ?>
    <?php else: ?>
        <p>No products found. <a href="add_product.php" class="add-product-link"><i class="fas fa-plus"></i> New product</a></p>
    <?php endif; ?>
</div>

<div class="orders">
    <h2>Orders for Your Products:</h2>
    <?php if ($ordersResult->num_rows > 0): ?>
        <?php while ($order = $ordersResult->fetch_assoc()): ?>
            <div class="order">
                <h3>Product: <?php echo htmlspecialchars($order['product_name']); ?></h3>
                <p>Buyer: <?php echo htmlspecialchars(explode('@', $order['buyer_username'])[0]); ?></p>
                <p>Order Date: <?php echo $order['order_date']; ?></p>
                <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                <?php if ($order['status'] === 'rejected'): ?>
                    <p>Rejection Reason: <?php echo htmlspecialchars($order['rejection_reason']); ?></p>
                <?php endif; ?>
                <?php if ($order['status'] === 'pending'): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                        <button type="button" class="btn btn-reject" onclick="showReasonForm(<?php echo $order['id']; ?>)">Reject</button>
                        <div id="reason-form-<?php echo $order['id']; ?>" class="form-reason">
                            <textarea name="reason" placeholder="Reason for rejection" required></textarea>
                            <button type="submit" name="action" value="reject">Submit</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No orders for your products.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> GreenTail Pets. All rights reserved.</p>
    <p>Your trusted partner in enhancing agriculture.</p>
</footer>

<script>
function showReasonForm(orderId) {
    document.getElementById('reason-form-' + orderId).style.display = 'block';
}
</script>

<?php
$conn->close();
?>
</body>
</html>
