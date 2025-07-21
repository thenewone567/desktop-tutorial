<?php
if (!has_permission($_SESSION['role'], 'manage_purchases')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$purchase_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT p.*, s.name as supplier_name, u.username as user_name
    FROM purchases p
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $purchase_id);
$stmt->execute();
$result = $stmt->get_result();
$purchase = $result->fetch_assoc();

$stmt = $conn->prepare("
    SELECT pi.*, p.name as product_name
    FROM purchase_items pi
    LEFT JOIN products p ON pi.product_id = p.id
    WHERE pi.purchase_id = ?
");
$stmt->bind_param("i", $purchase_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Details #<?php echo $purchase['id']; ?></h1>
</div>

<div class="row">
    <div class="col-md-6">
        <p><strong>Date:</strong> <?php echo $purchase['purchase_date']; ?></p>
        <p><strong>Supplier:</strong> <?php echo $purchase['supplier_name']; ?></p>
        <p><strong>User:</strong> <?php echo $purchase['user_name']; ?></p>
        <?php if ($purchase['supplier_invoice']): ?>
            <p><strong>Invoice:</strong> <a href="<?php echo $purchase['supplier_invoice']; ?>" target="_blank">View Invoice</a></p>
        <?php endif; ?>
    </div>
</div>

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
        <?php while ($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo $item['rate']; ?></td>
                <td><?php echo $item['quantity'] * $item['rate']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
