<?php
if (!has_permission($_SESSION['role'], 'manage_sales')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = $_POST['sale_id'];
    $user_id = $_SESSION['user_id'];
    $reason = $_POST['reason'];
    $refund_amount = $_POST['refund_amount'];
    $refund_method = $_POST['refund_method'];
    $products = json_decode($_POST['products'], true);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO sales_returns (sale_id, user_id, reason, refund_amount, refund_method, return_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisds", $sale_id, $user_id, $reason, $refund_amount, $refund_method);
        $stmt->execute();
        $sales_return_id = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO sales_return_items (sales_return_id, product_id, quantity) VALUES (?, ?, ?)");
        foreach ($products as $product) {
            $stmt->bind_param("iii", $sales_return_id, $product['id'], $product['quantity']);
            $stmt->execute();

            $stmt2 = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            $stmt2->bind_param("ii", $product['quantity'], $product['id']);
            $stmt2->execute();
        }

        $conn->commit();
        log_activity($_SESSION['user_id'], "Created new sales return for sale id: $sale_id");
        redirect('index.php?page=sales_returns');
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Failed to create sales return";
    }
}

$sales = $conn->query("SELECT id FROM sales ORDER BY id DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">New Sales Return</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" id="sales_return_form">
    <input type="hidden" name="products" id="products_input">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="sale_id" class="form-label">Sale ID</label>
                <select class="form-select" id="sale_id" name="sale_id" required>
                    <option value="">Select Sale ID</option>
                    <?php while ($sale = $sales->fetch_assoc()): ?>
                        <option value="<?php echo $sale['id']; ?>"><?php echo $sale['id']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div id="sale_items_container"></div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
                <input type="text" class="form-control" id="reason" name="reason">
            </div>
            <div class="mb-3">
                <label for="refund_amount" class="form-label">Refund Amount</label>
                <input type="number" class="form-control" id="refund_amount" name="refund_amount" step="0.01">
            </div>
            <div class="mb-3">
                <label for="refund_method" class="form-label">Refund Method</label>
                <select class="form-select" id="refund_method" name="refund_method">
                    <option value="Cash">Cash</option>
                    <option value="Store Credit">Store Credit</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create Sales Return</button>
        </div>
    </div>
</form>

<script>
    const saleIdSelect = document.getElementById('sale_id');
    const saleItemsContainer = document.getElementById('sale_items_container');
    const productsInput = document.getElementById('products_input');
    const salesReturnForm = document.getElementById('sales_return_form');

    let products = [];

    saleIdSelect.addEventListener('change', () => {
        const saleId = saleIdSelect.value;
        if (saleId) {
            fetch(`ajax.php?action=get_sale_items&sale_id=${saleId}`)
                .then(response => response.json())
                .then(data => {
                    saleItemsContainer.innerHTML = '';
                    products = data;
                    renderReturnItems();
                });
        } else {
            saleItemsContainer.innerHTML = '';
        }
    });

    function renderReturnItems() {
        saleItemsContainer.innerHTML = '';
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
        saleItemsContainer.appendChild(table);
    }

    function updateReturnQuantity(productId, quantity) {
        const product = products.find(p => p.product_id === productId);
        if (product) {
            product.return_quantity = quantity;
        }
    }

    salesReturnForm.addEventListener('submit', (e) => {
        const returnProducts = products.filter(p => p.return_quantity > 0).map(p => ({
            id: p.product_id,
            quantity: p.return_quantity
        }));
        productsInput.value = JSON.stringify(returnProducts);
    });
</script>
