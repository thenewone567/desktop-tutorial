<?php
$conn = get_db_connection();
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Inventory</h1>

<a href="index.php?page=add_product" class="btn btn-primary mb-3">Add Product</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>SKU</th>
            <th>Barcode</th>
            <th>Category</th>
            <th>Purchase Rate</th>
            <th>Selling Rate</th>
            <th>Quantity</th>
            <th>Aisle</th>
            <th>Rack</th>
            <th>Bin</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['sku']; ?></td>
                <td><?php echo $product['barcode']; ?></td>
                <td><?php echo $product['category']; ?></td>
                <td><?php echo $product['purchase_rate']; ?></td>
                <td><?php echo $product['selling_rate']; ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td><?php echo $product['aisle']; ?></td>
                <td><?php echo $product['rack']; ?></td>
                <td><?php echo $product['bin']; ?></td>
                <td>
                    <a href="index.php?page=edit_product&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="index.php?page=delete_product&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
