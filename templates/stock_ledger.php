<?php
if (!has_permission($_SESSION['role'], 'manage_products')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT 'Purchase' as type, pi.id, p.name as product_name, pi.quantity, pr.purchase_date as date
    FROM purchase_items pi
    JOIN purchases pr ON pi.purchase_id = pr.id
    JOIN products p ON pi.product_id = p.id
    UNION ALL
    SELECT 'Sale' as type, si.id, p.name as product_name, -si.quantity as quantity, s.sale_date as date
    FROM sale_items si
    JOIN sales s ON si.sale_id = s.id
    JOIN products p ON si.product_id = p.id
    UNION ALL
    SELECT 'Sales Return' as type, sri.id, p.name as product_name, sri.quantity, sr.return_date as date
    FROM sales_return_items sri
    JOIN sales_returns sr ON sri.sales_return_id = sr.id
    JOIN products p ON sri.product_id = p.id
    UNION ALL
    SELECT 'Purchase Return' as type, pri.id, p.name as product_name, -pri.quantity as quantity, pr.return_date as date
    FROM purchase_return_items pri
    JOIN purchase_returns pr ON pri.purchase_return_id = pr.id
    JOIN products p ON pri.product_id = p.id
    UNION ALL
    SELECT 'Adjustment' as type, sa.id, p.name as product_name, sa.adjustment as quantity, sa.date
    FROM stock_adjustments sa
    JOIN products p ON sa.product_id = p.id
    ORDER BY date DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stock Ledger</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Product</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
