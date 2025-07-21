<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT p.name, p.sku, p.quantity, c.name as category_name, w.name as warehouse_location_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN warehouse_locations w ON p.warehouse_location_id = w.id
    ORDER BY p.name
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stock Report</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Warehouse Location</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['sku']; ?></td>
                    <td><?php echo $row['category_name']; ?></td>
                    <td><?php echo $row['warehouse_location_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
