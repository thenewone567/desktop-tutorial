<?php
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = $_POST['customer'];
    $sale_date = $_POST['sale_date'];
    $discount = $_POST['discount'];
    $tax = $_POST['tax'];
    $payment_method = $_POST['payment_method'];

    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    $total = 0;
    $items = [];
    for ($i = 0; $i < count($product_ids); $i++) {
        $product_id = $product_ids[$i];
        $quantity = $quantities[$i];

        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product['quantity'] >= $quantity) {
            $rate = $product['selling_rate'];
            $total += $quantity * $rate;
            $items[] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'rate' => $rate
            ];
        } else {
            die("Not enough stock for product ID " . $product_id);
        }
    }

    $grand_total = $total - $discount + $tax;

    $sql = "INSERT INTO sales (customer, sale_date, total, discount, tax, grand_total, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddsss", $customer, $sale_date, $total, $discount, $tax, $grand_total, $payment_method);
    $stmt->execute();
    $sale_id = $stmt->insert_id;

    foreach ($items as $item) {
        $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, rate) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $sale_id, $item['product_id'], $item['quantity'], $item['rate']);
        $stmt->execute();

        $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt->execute();
    }

    redirect('index.php?page=sales');
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Add Sale</h1>

<form method="post">
    <div class="mb-3">
        <label for="customer" class="form-label">Customer</label>
        <input type="text" class="form-control" id="customer" name="customer">
    </div>
    <div class="mb-3">
        <label for="sale_date" class="form-label">Date</label>
        <input type="date" class="form-control" id="sale_date" name="sale_date" required>
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

    <div class="row mt-3">
        <div class="col-md-4">
            <label for="discount" class="form-label">Discount</label>
            <input type="number" class="form-control" id="discount" name="discount" step="0.01" value="0" required>
        </div>
        <div class="col-md-4">
            <label for="tax" class="form-label">Tax</label>
            <input type="number" class="form-control" id="tax" name="tax" step="0.01" value="0" required>
        </div>
        <div class="col-md-4">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select class="form-select" id="payment_method" name="payment_method" required>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="UPI">UPI</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Add Sale</button>
    <a href="index.php?page=sales" class="btn btn-secondary mt-3">Cancel</a>
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
