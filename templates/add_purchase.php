<?php
if (!has_permission($_SESSION['role'], 'manage_purchases')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $user_id = $_SESSION['user_id'];
    $products = json_decode($_POST['products'], true);
    $supplier_invoice = '';

    if (isset($_FILES['supplier_invoice']) && $_FILES['supplier_invoice']['error'] === UPLOAD_ERR_OK) {
        $supplier_invoice = 'uploads/invoices/' . basename($_FILES['supplier_invoice']['name']);
        move_uploaded_file($_FILES['supplier_invoice']['tmp_name'], $supplier_invoice);
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO purchases (supplier_id, user_id, supplier_invoice, purchase_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $supplier_id, $user_id, $supplier_invoice);
        $stmt->execute();
        $purchase_id = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, rate) VALUES (?, ?, ?, ?)");
        foreach ($products as $product) {
            $stmt->bind_param("iiid", $purchase_id, $product['id'], $product['quantity'], $product['rate']);
            $stmt->execute();

            $stmt2 = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            $stmt2->bind_param("ii", $product['quantity'], $product['id']);
            $stmt2->execute();
        }

        $conn->commit();
        log_activity($_SESSION['user_id'], "Created new purchase with id: $purchase_id");
        redirect('index.php?page=purchases');
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Failed to create purchase";
    }
}

$suppliers = $conn->query("SELECT * FROM suppliers");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">New Purchase</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <input type="text" id="product_search" class="form-control" placeholder="Search for products by name, SKU, or barcode">
        </div>
        <div id="product_suggestions"></div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="purchase_items">
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <form method="post" id="purchase_form" enctype="multipart/form-data">
            <input type="hidden" name="products" id="products_input">
            <div class="mb-3">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select class="form-select" id="supplier_id" name="supplier_id" required>
                    <option value="">Select Supplier</option>
                    <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                        <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="supplier_invoice" class="form-label">Supplier Invoice</label>
                <input type="file" class="form-control" id="supplier_invoice" name="supplier_invoice">
            </div>
            <button type="submit" class="btn btn-primary">Create Purchase</button>
        </form>
    </div>
</div>

<script>
    const productSearch = document.getElementById('product_search');
    const productSuggestions = document.getElementById('product_suggestions');
    const purchaseItems = document.getElementById('purchase_items');
    const productsInput = document.getElementById('products_input');
    const purchaseForm = document.getElementById('purchase_form');

    let products = [];

    productSearch.addEventListener('input', () => {
        const query = productSearch.value;
        if (query.length > 2) {
            fetch(`ajax.php?action=search_products&query=${query}`)
                .then(response => response.json())
                .then(data => {
                    productSuggestions.innerHTML = '';
                    data.forEach(product => {
                        const div = document.createElement('div');
                        div.innerHTML = `${product.name} - ${product.sku}`;
                        div.classList.add('suggestion');
                        div.addEventListener('click', () => {
                            addProduct(product);
                            productSearch.value = '';
                            productSuggestions.innerHTML = '';
                        });
                        productSuggestions.appendChild(div);
                    });
                });
        } else {
            productSuggestions.innerHTML = '';
        }
    });

    function addProduct(product) {
        const existingProduct = products.find(p => p.id === product.id);
        if (existingProduct) {
            existingProduct.quantity++;
        } else {
            products.push({
                id: product.id,
                name: product.name,
                rate: product.purchase_rate,
                quantity: 1,
            });
        }
        renderPurchaseItems();
    }

    function removeProduct(productId) {
        products = products.filter(p => p.id !== productId);
        renderPurchaseItems();
    }

    function updateQuantity(productId, quantity) {
        const product = products.find(p => p.id === productId);
        if (product) {
            product.quantity = quantity;
        }
        renderPurchaseItems();
    }

    function renderPurchaseItems() {
        purchaseItems.innerHTML = '';
        products.forEach(product => {
            const tr = document.createElement('tr');
            const totalForRow = product.quantity * product.rate;
            tr.innerHTML = `
                <td>${product.name}</td>
                <td><input type="number" value="${product.quantity}" min="1" onchange="updateQuantity(${product.id}, this.value)"></td>
                <td><input type="number" value="${product.rate}" step="0.01" onchange="updateRate(${product.id}, this.value)"></td>
                <td>${totalForRow.toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeProduct(${product.id})">Remove</button></td>
            `;
            purchaseItems.appendChild(tr);
        });
    }

    function updateRate(productId, rate) {
        const product = products.find(p => p.id === productId);
        if (product) {
            product.rate = rate;
        }
        renderPurchaseItems();
    }

    purchaseForm.addEventListener('submit', (e) => {
        productsInput.value = JSON.stringify(products);
    });
</script>
