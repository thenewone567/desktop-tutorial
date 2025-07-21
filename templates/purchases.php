<?php
if (!has_permission($_SESSION['role'], 'manage_purchases')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT p.*, s.name as supplier_name, u.username as user_name
    FROM purchases p
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY p.purchase_date DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchases</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=add_purchase" class="btn btn-sm btn-outline-secondary">
            New Purchase
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Supplier</th>
                <th>User</th>
                <th>Invoice</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['purchase_date']; ?></td>
                    <td><?php echo $row['supplier_name']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                    <td>
                        <?php if ($row['supplier_invoice']): ?>
                            <a href="<?php echo $row['supplier_invoice']; ?>" target="_blank">View Invoice</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?page=purchase_details&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary">View Details</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
