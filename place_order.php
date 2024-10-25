<?php
session_start();
include 'db.php';
if ($_SESSION['role'] !== 'buyer') {
    header('Location: login.php');
    exit();
}

$id = $_GET['id']; 
$buyer_id = $_SESSION['user_id'];
$order_date = date('Y-m-d H:i:s');


$sql = "INSERT INTO orders (product_id, buyer_id, order_date) VALUES ($id, $buyer_id, '$order_date')";
echo $sql; 

$stmt = $conn->prepare("INSERT INTO orders (product_id, buyer_id, order_date) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $id, $buyer_id, $order_date); 
if ($stmt->execute() === TRUE) {
    $_SESSION['success_message'] = "Order placed successfully!"; 
} else {
    $_SESSION['error_message'] = "Error placing order: " . $stmt->error; 
}

$stmt->close();

header('Location: buyer.php');
exit(); 
?>
