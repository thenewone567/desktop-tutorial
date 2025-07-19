<?php
$conn = get_db_connection();
$today = date('Y-m-d');
$seven_days_ago = date('Y-m-d', strtotime('-7 days'));
$month_start = date('Y-m-01');

$daily_sales_sql = "SELECT * FROM sales WHERE sale_date = '$today'";
$daily_sales_result = $conn->query($daily_sales_sql);
$daily_sales = $daily_sales_result->fetch_all(MYSQLI_ASSOC);

$weekly_sales_sql = "SELECT * FROM sales WHERE sale_date BETWEEN '$seven_days_ago' AND '$today'";
$weekly_sales_result = $conn->query($weekly_sales_sql);
$weekly_sales = $weekly_sales_result->fetch_all(MYSQLI_ASSOC);

$monthly_sales_sql = "SELECT * FROM sales WHERE sale_date >= '$month_start'";
$monthly_sales_result = $conn->query($monthly_sales_sql);
$monthly_sales = $monthly_sales_result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Sales Report</h1>

<h2>Daily Sales</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($daily_sales as $sale): ?>
            <tr>
                <td><?php echo $sale['id']; ?></td>
                <td><?php echo $sale['customer']; ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td><?php echo $sale['grand_total']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Weekly Sales</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($weekly_sales as $sale): ?>
            <tr>
                <td><?php echo $sale['id']; ?></td>
                <td><?php echo $sale['customer']; ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td><?php echo $sale['grand_total']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Monthly Sales</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($monthly_sales as $sale): ?>
            <tr>
                <td><?php echo $sale['id']; ?></td>
                <td><?php echo $sale['customer']; ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td><?php echo $sale['grand_total']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="index.php?page=reports" class="btn btn-secondary">Back to Reports</a>

<?php include 'footer.php'; ?>
