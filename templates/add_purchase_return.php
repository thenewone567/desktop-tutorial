<?php
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase_id = $_POST['purchase_id'];
    $reason = $_POST['reason'];
    $products = json_decode($_POST['products'], true);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO purchase_returns (purchase_id, reason, return_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $purchase_id, $reason);
        $stmt->execute();
        $purchase_return_id = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO purchase_return_items (purchase_return_id, product_id, quantity) VALUES (?, ?, ?)");
        foreach ($products as $product) {
            $stmt->bind_param("iii", $purchase_return_id, $product['id'], $product['quantity']);
            $stmt->execute();

            $stmt2 = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt2->bind_param("ii", $product['quantity'], $product['id']);
            $stmt2->execute();
        }

        $conn->commit();
        redirect('index.php?page=purchase_returns');
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Failed to create purchase return";
    }
}

$purchases = $conn->query("SELECT id FROM purchases ORDER BY id DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">New Purchase Return</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" id="purchase_return_form">
    <input type="hidden" name="products" id="products_input">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="purchase_id" class="form-label">Purchase ID</label>
                <select class="form-select" id="purchase_id" name="purchase_id" required>
                    <option value="">Select Purchase ID</option>
                    <?php while ($purchase = $purchases->fetch_assoc()): ?>
                        <option value="<?php echo $purchase['id']; ?>"><?php echo $purchase['id']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div id="purchase_items_container"></div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
                <input type="text" class="form-control" id="reason" name="reason">
            </div>
            <button type="submit" class="btn btn-primary">Create Purchase Return</button>
        </div>
    </div>
</form>

<script>
    const purchaseIdSelect = document.getElementById('purchase_id');
    const purchaseItemsContainer = document.getElementById('purchase_items_container');
    const productsInput = document.getElementById('products_input');
    const purchaseReturnForm = document.getElementById('purchase_return_form');

    let products = [];

    purchaseIdSelect.addEventListener('change', () => {
        const purchaseId = purchaseIdSelect.value;
        if (purchaseId) {
            fetch(`ajax.php?action=get_purchase_items&purchase_id=${purchaseId}`)
                .then(response => response.json())
                .then(data => {
                    purchaseItemsContainer.innerHTML = '';
                    products = data;
                    renderReturnItems();
                });
        } else {
            purchaseItemsContainer.innerHTML = '';
        }
    });

    function renderReturnItems() {
        purchaseItemsContainer.innerHTML = '';
        const table = document.createElement('table');
        table.classList.add('table', 'table-bordered');
        table.innerHTML = `
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Return Quantity</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        `;
        const tbody = table.querySelector('tbody');
        products.forEach(product => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${product.product_name}</td>
                <td>${product.quantity}</td>
                <td><input type="number" value="0" min="0" max="${product.quantity}" onchange="updateReturnQuantity(${product.product_id}, this.value)"></td>
            `;
            tbody.appendChild(tr);
        });
        purchaseItemsContainer.appendChild(table);
    }

    function updateReturnQuantity(productId, quantity) {
        const product = products.find(p => p.product_id === productId);
        if (product) {
            product.return_quantity = quantity;
        }
    }

    purchaseReturnForm.addEventListener('submit', (e) => {
        const returnProducts = products.filter(p => p.return_quantity > 0).map(p => ({
            id: p.product_id,
            quantity: p.return_quantity
        }));
        productsInput.value = JSON.stringify(returnProducts);
    });
</script>
