<?php
$conn = get_db_connection();
$sql = "SELECT * FROM sales_returns";
$result = $conn->query($sql);
$sales_returns = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Sales Returns</h1>

<a href="index.php?page=add_sales_return" class="btn btn-primary mb-3">Add Sales Return</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sale ID</th>
            <th>Date</th>
            <th>Items</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales_returns as $sales_return): ?>
            <tr>
                <td><?php echo $sales_return['id']; ?></td>
                <td><?php echo $sales_return['sale_id']; ?></td>
                <td><?php echo $sales_return['return_date']; ?></td>
                <td>
                    <?php
                    $return_id = $sales_return['id'];
                    $item_sql = "SELECT sri.*, p.name FROM sales_return_items sri JOIN products p ON sri.product_id = p.id WHERE sri.sales_return_id = $return_id";
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
