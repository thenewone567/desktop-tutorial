<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT name, quantity, purchase_rate, (quantity * purchase_rate) as valuation
    FROM products
    ORDER BY name
");

$total_valuation = $conn->query("SELECT SUM(quantity * purchase_rate) as total FROM products")->fetch_assoc()['total'] ?? 0;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Inventory Valuation Report</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Purchase Rate</th>
                <th>Valuation</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['purchase_rate']; ?></td>
                    <td><?php echo $row['valuation']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total Valuation</th>
                <th><?php echo number_format($total_valuation, 2); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
