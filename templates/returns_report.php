<?php
$conn = get_db_connection();

$sales_returns_sql = "SELECT * FROM sales_returns";
$sales_returns_result = $conn->query($sales_returns_sql);
$sales_returns = $sales_returns_result->fetch_all(MYSQLI_ASSOC);

$purchase_returns_sql = "SELECT * FROM purchase_returns";
$purchase_returns_result = $conn->query($purchase_returns_sql);
$purchase_returns = $purchase_returns_result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Returns Report</h1>

<h2>Sales Returns</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sale ID</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales_returns as $sales_return): ?>
            <tr>
                <td><?php echo $sales_return['id']; ?></td>
                <td><?php echo $sales_return['sale_id']; ?></td>
                <td><?php echo $sales_return['return_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Purchase Returns</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Purchase ID</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($purchase_returns as $purchase_return): ?>
            <tr>
                <td><?php echo $purchase_return['id']; ?></td>
                <td><?php echo $purchase_return['purchase_id']; ?></td>
                <td><?php echo $purchase_return['return_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="index.php?page=reports" class="btn btn-secondary">Back to Reports</a>

<?php include 'footer.php'; ?>
