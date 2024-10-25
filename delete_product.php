<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $checkOrdersSql = "SELECT COUNT(*) as orderCount FROM orders WHERE product_id = '$product_id'";
    $result = $conn->query($checkOrdersSql);
    $orderCount = $result->fetch_assoc()['orderCount'];

    if ($orderCount > 0) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Error Deleting Product</title>
            <link rel="stylesheet" href="css/style.css"> 
            <style>
                body {
                    background-color: #f0f8ff;
                    color: #333;
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 50px; 
                }
                .error-message {
                    background-color: #ffdddd; 
                    color: #d8000c; 
                    border: 1px solid #d8000c; 
                    padding: 15px;
                    margin: 20px auto;
                    border-radius: 5px; 
                    display: inline-block; 
                    width: 80%; 
                }
                .link {
                    color: #007BFF; 
                    text-decoration: none; 
                    font-weight: bold; 
                }
                .link:hover {
                    text-decoration: underline; 
                }
            </style>
        </head>
        <body>
            <div class="error-message">
                <p>Cannot delete the product because there are orders associated with it.</p>
                <p><a href="seller.php" class="link">Return to your dashboard</a></p>
            </div>
        </body>
        </html>
        ';
    } else {
        $sql = "DELETE FROM products WHERE id = '$product_id' AND seller_id = '" . $_SESSION['user_id'] . "'";
        if ($conn->query($sql) === TRUE) {
            header('Location: seller.php'); 
            exit();
        } else {
            echo "Error deleting product: " . $conn->error;
        }
    }
} else {
    echo "Product ID not specified.";
}

$conn->close();
?>
