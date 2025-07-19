<?php
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase_id = $_POST['purchase_id'];
    $return_date = $_POST['return_date'];

    $sql = "INSERT INTO purchase_returns (purchase_id, return_date) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $purchase_id, $return_date);
    $stmt->execute();
    $purchase_return_id = $stmt->insert_id;

    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    for ($i = 0; $i < count($product_ids); $i++) {
        $product_id = $product_ids[$i];
        $quantity = $quantities[$i];

        $sql = "INSERT INTO purchase_return_items (purchase_return_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $purchase_return_id, $product_id, $quantity);
        $stmt->execute();

        $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();
    }

    redirect('index.php?page=purchase_returns');
}

$sql = "SELECT * FROM purchases";
$result = $conn->query($sql);
$purchases = $result->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Add Purchase Return</h1>

<form method="post">
    <div class="mb-3">
        <label for="purchase_id" class="form-label">Purchase ID</label>
        <select class="form-select" id="purchase_id" name="purchase_id" required>
            <?php foreach ($purchases as $purchase): ?>
                <option value="<?php echo $purchase['id']; ?>"><?php echo $purchase['id']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="return_date" class="form-label">Date</label>
        <input type="date" class="form-control" id="return_date" name="return_date" required>
    </div>

    <h2>Items</h2>
    <table class="table" id="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select class="form-select" name="product_id[]" required>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" class="form-control" name="quantity[]" required></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">Remove</button></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="btn btn-secondary" onclick="addItem()">Add Item</button>
    <br>

    <button type="submit" class="btn btn-primary mt-3">Add Purchase Return</button>
    <a href="index.php?page=purchase_returns" class="btn btn-secondary mt-3">Cancel</a>
</form>

<script>
    function addItem() {
        var table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
        var newRow = table.insertRow();
        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);

        cell1.innerHTML = `
            <select class="form-select" name="product_id[]" required>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                <?php endforeach; ?>
            </select>
        `;
        cell2.innerHTML = '<input type="number" class="form-control" name="quantity[]" required>';
        cell3.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">Remove</button>';
    }

    function removeItem(button) {
        var row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }
</script>

<?php include 'footer.php'; ?>
