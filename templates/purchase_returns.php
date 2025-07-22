<?php
$conn = get_db_connection();

$result = $conn->query("
    SELECT pr.*
    FROM purchase_returns pr
    ORDER BY pr.return_date DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Returns</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=add_purchase_return" class="btn btn-sm btn-outline-secondary">
            New Purchase Return
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Purchase ID</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['return_date']; ?></td>
                    <td><?php echo $row['purchase_id']; ?></td>
                    <td><?php echo $row['reason']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
