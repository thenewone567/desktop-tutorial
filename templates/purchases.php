<?php
$conn = get_db_connection();
$sql = "SELECT * FROM purchases";
$result = $conn->query($sql);
$purchases = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Purchases</h1>

<a href="index.php?page=add_purchase" class="btn btn-primary mb-3">Add Purchase</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Items</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($purchases as $purchase): ?>
            <tr>
                <td><?php echo $purchase['id']; ?></td>
                <td><?php echo $purchase['supplier']; ?></td>
                <td><?php echo $purchase['purchase_date']; ?></td>
                <td>
                    <?php
                    $purchase_id = $purchase['id'];
                    $item_sql = "SELECT pi.*, p.name FROM purchase_items pi JOIN products p ON pi.product_id = p.id WHERE pi.purchase_id = $purchase_id";
                    $item_result = $conn->query($item_sql);
                    $items = $item_result->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo $item['rate']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
