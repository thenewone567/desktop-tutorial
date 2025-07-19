<?php
$conn = get_db_connection();
$sql = "SELECT * FROM purchase_returns";
$result = $conn->query($sql);
$purchase_returns = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Purchase Returns</h1>

<a href="index.php?page=add_purchase_return" class="btn btn-primary mb-3">Add Purchase Return</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Purchase ID</th>
            <th>Date</th>
            <th>Items</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($purchase_returns as $purchase_return): ?>
            <tr>
                <td><?php echo $purchase_return['id']; ?></td>
                <td><?php echo $purchase_return['purchase_id']; ?></td>
                <td><?php echo $purchase_return['return_date']; ?></td>
                <td>
                    <?php
                    $return_id = $purchase_return['id'];
                    $item_sql = "SELECT pri.*, p.name FROM purchase_return_items pri JOIN products p ON pri.product_id = p.id WHERE pri.purchase_return_id = $return_id";
                    $item_result = $conn->query($item_sql);
                    $items = $item_result->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
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
