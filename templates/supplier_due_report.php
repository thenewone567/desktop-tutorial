<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT s.name, SUM(pi.quantity * pi.rate) as total_purchase
    FROM suppliers s
    JOIN purchases p ON s.id = p.supplier_id
    JOIN purchase_items pi ON p.id = pi.purchase_id
    GROUP BY s.id
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Supplier Due Report</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Total Purchase</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['total_purchase']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
