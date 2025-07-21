<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT p.name, SUM(si.quantity) as total_sold, SUM(si.quantity * si.rate) as total_revenue
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    GROUP BY p.id
    ORDER BY total_sold DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Item Performance Report</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Total Sold</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['total_sold']; ?></td>
                    <td><?php echo $row['total_revenue']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
