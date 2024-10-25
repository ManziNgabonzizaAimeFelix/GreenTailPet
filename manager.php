<?php
session_start();
include 'db.php';

// Check if user is a manager
if ($_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit();
}

// Fetch product and order data
$sql_products = "SELECT products.*, users.username AS seller_name, categories.category_name 
                 FROM products 
                 JOIN users ON products.seller_id = users.id 
                 JOIN categories ON products.category_id = categories.id";
$result_products = $conn->query($sql_products);

$sql_orders = "SELECT orders.*, products.name AS product_name, users.username AS buyer_name 
               FROM orders 
               JOIN products ON orders.product_id = products.id 
               JOIN users ON orders.buyer_id = users.id";
$result_orders = $conn->query($sql_orders);

// Remove seller functionality
if (isset($_GET['remove_seller'])) {
    $seller_id = $_GET['remove_seller'];
    $conn->query("DELETE FROM users WHERE id = $seller_id");
    header('Location: manager.php');
    exit();
}

// Remove product functionality
if (isset($_GET['remove_product'])) {
    $product_id = $_GET['remove_product'];
    $conn->query("DELETE FROM products WHERE id = $product_id");
    header('Location: manager.php');
    exit();
}

// PDF Download for Orders
if (isset($_GET['download_orders'])) {
    require('fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Order ID');
    $pdf->Cell(40, 10, 'Product Name');
    $pdf->Cell(40, 10, 'Buyer Name');
    $pdf->Cell(40, 10, 'Order Date');
    $pdf->Ln();

    foreach ($result_orders as $row) {
        $pdf->Cell(40, 10, $row['id']);
        $pdf->Cell(40, 10, $row['product_name']);
        $pdf->Cell(40, 10, $row['buyer_name']);
        $pdf->Cell(40, 10, $row['order_date']);
        $pdf->Ln();
    }

    $pdf->Output('D', 'orders_report.pdf');
    exit();
}

// PDF Download for Products
if (isset($_GET['download_products'])) {
    require('fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Product ID');
    $pdf->Cell(40, 10, 'Product Name');
    $pdf->Cell(40, 10, 'Category');
    $pdf->Cell(40, 10, 'Seller Name');
    $pdf->Ln();

    foreach ($result_products as $row) {
        $pdf->Cell(40, 10, $row['id']);
        $pdf->Cell(40, 10, $row['name']);
        $pdf->Cell(40, 10, $row['category_name']);
        $pdf->Cell(40, 10, $row['seller_name']);
        $pdf->Ln();
    }

    $pdf->Output('D', 'products_report.pdf');
    exit();
}

// Excel Download for Orders
if (isset($_GET['download_orders_excel'])) {
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Orders Report');

    // Set the headers
    $sheet->setCellValue('A1', 'Order ID')
          ->setCellValue('B1', 'Product Name')
          ->setCellValue('C1', 'Buyer Name')
          ->setCellValue('D1', 'Order Date');

    $rowNumber = 2;
    while ($row = $result_orders->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $row['id']);
        $sheet->setCellValue('B' . $rowNumber, $row['product_name']);
        $sheet->setCellValue('C' . $rowNumber, $row['buyer_name']);
        $sheet->setCellValue('D' . $rowNumber, $row['order_date']);
        $rowNumber++;
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'orders_report.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    $writer->save('php://output');
    exit();
}

// Excel Download for Products
if (isset($_GET['download_products_excel'])) {
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Products Report');

    // Set the headers
    $sheet->setCellValue('A1', 'Product ID')
          ->setCellValue('B1', 'Product Name')
          ->setCellValue('C1', 'Category')
          ->setCellValue('D1', 'Seller Name')
          ->setCellValue('E1', 'Price');

    $rowNumber = 2;
    while ($row = $result_products->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $row['id']);
        $sheet->setCellValue('B' . $rowNumber, $row['name']);
        $sheet->setCellValue('C' . $rowNumber, $row['category_name']);
        $sheet->setCellValue('D' . $rowNumber, $row['seller_name']);
        $sheet->setCellValue('E' . $rowNumber, $row['price']);
        $rowNumber++;
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'products_report.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    $writer->save('php://output');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard - GreenTail Pets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome for icons -->
    <style>
        body {
            background-color: #f0f8ff; 
            color: #333; 
            font-family: Arial, sans-serif; 
            text-align: center; 
        }
        .container {
            width: 90%;
            margin: 0 auto;
        }

        h1 {
            font-size: 2.5rem;
            text-align: center;
            margin: 20px 0;
            color: #2c3e50; 
        }

        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #2d3436;
            text-align: center;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th, .data-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .data-table th {
            background-color: #2d3436;
            color: white;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .data-table tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            display: inline-block;
            background-color: #00b894;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-bottom: 20px; 
            max-width: 200px;
        }

        .btn:hover {
            background-color: #55efc4;
        }

        .btn.remove {
            background-color: #e74c3c;
        }

        .btn.remove:hover {
            background-color: #c0392b;
        }

        nav ul.nav-bar {
            display: flex;
            justify-content: space-between;
            list-style: none;
            padding: 10px;
            background-color: #2d3436;
        }

        nav ul.nav-bar li {
            display: inline-block;
            margin-right: 15px;
        }

        nav ul.nav-bar li a {
            color: white;
            text-decoration: none;
        }

        nav ul.nav-bar li a.logout-icon {
            display: flex;
            align-items: center;
        }

        nav ul.nav-bar li a i {
            margin-right: 8px;
        }

        .logo {
            max-width: 50px; 
            height: auto; 
            margin-right: 20px; 
            border-radius: 50px;
        }

        .section .btn {
            margin-bottom: 20px;
            margin-left: auto;
            margin-right: auto;
            display: block;
            text-align: center;
        }

        .icon-btn {
            color: #e74c3c; 
            font-size: 20px; 
            text-decoration: none; 
            padding: 5px; 
            border-radius: 5px; 
            transition: background-color 0.3s ease, color 0.3s ease; 
        }

        .icon-btn:hover {
            background-color: #e74c3c; 
            color: white; 
        }

        .btn i {
            margin-right: 8px; 
        }

        footer {
            background-color: #2d3436; 
            color: white; 
            text-align: center;
            padding: 20px 0; 
            position: relative; 
            bottom: 0; 
            width: 100%; 
        }
    </style>
</head>
<body>
    <nav>
        <ul class="nav-bar">
            <li><img src="logo.png" alt="GreenTail Pets Logo" class="logo"></li>
            <li><a href="manager.php">Dashboard</a></li>
            <li><a href="logout.php" class="logout-icon"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Manager Dashboard</h1>
        <section class="section products">
            <h2>Product List</h2>
            <a href="manager.php?download_products=true" class="btn">
                <i class="fas fa-download"></i> Download Product Report (PDF)
            </a>
            <a href="manager.php?download_products_excel=true" class="btn">
                <i class="fas fa-download"></i> Download Product Report (Excel)
            </a>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Seller Name</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_products->num_rows > 0): ?>
                        <?php while ($product = $result_products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['seller_name']); ?></td>
                            <td><?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <a href="manager.php?remove_product=<?php echo $product['id']; ?>" class="icon-btn" title="Remove Product">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <section class="section orders">
            <h2>Order List</h2>
            <a href="manager.php?download_orders=true" class="btn">
                <i class="fas fa-download"></i> Download Order Report (PDF)
            </a>
            <a href="manager.php?download_orders_excel=true" class="btn">
                <i class="fas fa-download"></i> Download Order Report (Excel)
            </a>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Buyer Name</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_orders->num_rows > 0): ?>
                        <?php while ($order = $result_orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <section class="section sellers">
            <h2>Seller List</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Seller ID</th>
                        <th>Seller Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_sellers = "SELECT * FROM users WHERE role = 'seller'";
                    $result_sellers = $conn->query($sql_sellers);
                    if ($result_sellers->num_rows > 0) {
                        while ($seller = $result_sellers->fetch_assoc()) {
                            $email = isset($seller['email']) ? htmlspecialchars($seller['email']) : 'N/A';
                    ?>
                    <tr>
                        <td><?php echo $seller['id']; ?></td>
                        <td><?php echo htmlspecialchars($seller['username']); ?></td>
                        <td><?php echo $email; ?></td>
                        <td>
                            <a href="manager.php?remove_seller=<?php echo $seller['id']; ?>" class="icon-btn" title="Remove Seller">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo '<tr><td colspan="4">No sellers found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
    <footer>
        <p>&copy; 2024 GreenTail Pets. All rights reserved.</p>
        <p><a href="privacy.php" style="color: #55efc4;">Privacy Policy</a></p>
    </footer>
</body>
</html>
