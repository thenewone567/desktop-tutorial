<?php
if (!has_permission($_SESSION['role'], 'manage_sales')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT sr.*, u.username as user_name
    FROM sales_returns sr
    LEFT JOIN users u ON sr.user_id = u.id
    ORDER BY sr.return_date DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales Returns</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=add_sales_return" class="btn btn-sm btn-outline-secondary">
            New Sales Return
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Sale ID</th>
                <th>User</th>
                <th>Reason</th>
                <th>Refund Amount</th>
                <th>Refund Method</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['return_date']; ?></td>
                    <td><?php echo $row['sale_id']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                    <td><?php echo $row['reason']; ?></td>
                    <td><?php echo $row['refund_amount']; ?></td>
                    <td><?php echo $row['refund_method']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
