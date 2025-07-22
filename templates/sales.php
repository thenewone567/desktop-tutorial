<?php
$conn = get_db_connection();

$result = $conn->query("
    SELECT s.*
    FROM sales s
    ORDER BY s.sale_date DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=add_sale" class="btn btn-sm btn-outline-secondary">
            New Sale
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Grand Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['sale_date']; ?></td>
                    <td><?php echo $row['customer']; ?></td>
                    <td><?php echo $row['grand_total']; ?></td>
                    <td>
                        <a href="index.php?page=invoice&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary">View Invoice</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
