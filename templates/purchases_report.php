<?php
$conn = get_db_connection();
$today = date('Y-m-d');
$seven_days_ago = date('Y-m-d', strtotime('-7 days'));
$month_start = date('Y-m-01');

$daily_purchases_sql = "SELECT * FROM purchases WHERE purchase_date = '$today'";
$daily_purchases_result = $conn->query($daily_purchases_sql);
$daily_purchases = $daily_purchases_result->fetch_all(MYSQLI_ASSOC);

$weekly_purchases_sql = "SELECT * FROM purchases WHERE purchase_date BETWEEN '$seven_days_ago' AND '$today'";
$weekly_purchases_result = $conn->query($weekly_purchases_sql);
$weekly_purchases = $weekly_purchases_result->fetch_all(MYSQLI_ASSOC);

$monthly_purchases_sql = "SELECT * FROM purchases WHERE purchase_date >= '$month_start'";
$monthly_purchases_result = $conn->query($monthly_purchases_sql);
$monthly_purchases = $monthly_purchases_result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Purchases Report</h1>

<h2>Daily Purchases</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($daily_purchases as $purchase): ?>
            <tr>
                <td><?php echo $purchase['id']; ?></td>
                <td><?php echo $purchase['supplier']; ?></td>
                <td><?php echo $purchase['purchase_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Weekly Purchases</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($weekly_purchases as $purchase): ?>
            <tr>
                <td><?php echo $purchase['id']; ?></td>
                <td><?php echo $purchase['supplier']; ?></td>
                <td><?php echo $purchase['purchase_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Monthly Purchases</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($monthly_purchases as $purchase): ?>
            <tr>
                <td><?php echo $purchase['id']; ?></td>
                <td><?php echo $purchase['supplier']; ?></td>
                <td><?php echo $purchase['purchase_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="index.php?page=reports" class="btn btn-secondary">Back to Reports</a>

<?php include 'footer.php'; ?>
