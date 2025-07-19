<?php
$conn = get_db_connection();
$sale_id = $_GET['id'];

$sql = "SELECT * FROM sales WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
$sale = $result->fetch_assoc();

$item_sql = "SELECT si.*, p.name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?";
$stmt = $conn->prepare($item_sql);
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$item_result = $stmt->get_result();
$items = $item_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Invoice</h1>
                <p><strong>Invoice #:</strong> <?php echo $sale['id']; ?></p>
                <p><strong>Date:</strong> <?php echo $sale['sale_date']; ?></p>
                <p><strong>Customer:</strong> <?php echo $sale['customer']; ?></p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo $item['rate']; ?></td>
                                <td><?php echo $item['quantity'] * $item['rate']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><strong>Subtotal:</strong> <?php echo $sale['total']; ?></p>
                <p><strong>Discount:</strong> <?php echo $sale['discount']; ?></p>
                <p><strong>Tax:</strong> <?php echo $sale['tax']; ?></p>
                <p><strong>Grand Total:</strong> <?php echo $sale['grand_total']; ?></p>
                <p><strong>Payment Method:</strong> <?php echo $sale['payment_method']; ?></p>
                <div class="footer text-center">
                    <p>Thank you for your business!</p>
                </div>
                <button class="btn btn-primary no-print" onclick="window.print()">Print Invoice</button>
            </div>
        </div>
    </div>
</body>
</html>
