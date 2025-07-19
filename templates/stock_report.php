<?php
$conn = get_db_connection();
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Stock Report</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>SKU</th>
            <th>Quantity</th>
            <th>Aisle</th>
            <th>Rack</th>
            <th>Bin</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['sku']; ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td><?php echo $product['aisle']; ?></td>
                <td><?php echo $product['rack']; ?></td>
                <td><?php echo $product['bin']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="index.php?page=reports" class="btn btn-secondary">Back to Reports</a>

<?php include 'footer.php'; ?>
