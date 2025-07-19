<?php
$conn = get_db_connection();
$sql = "SELECT * FROM sales";
$result = $conn->query($sql);
$sales = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Sales</h1>

<a href="index.php?page=add_sale" class="btn btn-primary mb-3">Add Sale</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total</th>
            <th>Discount</th>
            <th>Tax</th>
            <th>Grand Total</th>
            <th>Payment Method</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?php echo $sale['id']; ?></td>
                <td><?php echo $sale['customer']; ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td><?php echo $sale['total']; ?></td>
                <td><?php echo $sale['discount']; ?></td>
                <td><?php echo $sale['tax']; ?></td>
                <td><?php echo $sale['grand_total']; ?></td>
                <td><?php echo $sale['payment_method']; ?></td>
                <td>
                    <a href="index.php?page=invoice&id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-info" target="_blank">View Invoice</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
