<?php
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = $_POST['supplier'];
    $purchase_date = $_POST['purchase_date'];

    $sql = "INSERT INTO purchases (supplier, purchase_date) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $supplier, $purchase_date);
    $stmt->execute();
    $purchase_id = $stmt->insert_id;

    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $rates = $_POST['rate'];

    for ($i = 0; $i < count($product_ids); $i++) {
        $product_id = $product_ids[$i];
        $quantity = $quantities[$i];
        $rate = $rates[$i];

        $sql = "INSERT INTO purchase_items (purchase_id, product_id, quantity, rate) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $purchase_id, $product_id, $quantity, $rate);
        $stmt->execute();

        $sql = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();
    }

    redirect('index.php?page=purchases');
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Add Purchase</h1>

<form method="post">
    <div class="mb-3">
        <label for="supplier" class="form-label">Supplier</label>
        <input type="text" class="form-control" id="supplier" name="supplier" required>
    </div>
    <div class="mb-3">
        <label for="purchase_date" class="form-label">Date</label>
        <input type="date" class="form-control" id="purchase_date" name="purchase_date" required>
    </div>

    <h2>Items</h2>
    <table class="table" id="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Rate</th>
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
                <td><input type="number" class="form-control" name="rate[]" step="0.01" required></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">Remove</button></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="btn btn-secondary" onclick="addItem()">Add Item</button>
    <br>

    <button type="submit" class="btn btn-primary mt-3">Add Purchase</button>
    <a href="index.php?page=purchases" class="btn btn-secondary mt-3">Cancel</a>
</form>

<script>
    function addItem() {
        var table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
        var newRow = table.insertRow();
        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);
        var cell4 = newRow.insertCell(3);

        cell1.innerHTML = `
            <select class="form-select" name="product_id[]" required>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                <?php endforeach; ?>
            </select>
        `;
        cell2.innerHTML = '<input type="number" class="form-control" name="quantity[]" required>';
        cell3.innerHTML = '<input type="number" class="form-control" name="rate[]" step="0.01" required>';
        cell4.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">Remove</button>';
    }

    function removeItem(button) {
        var row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }
</script>

<?php include 'footer.php'; ?>
