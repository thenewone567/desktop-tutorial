<?php
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = $_POST['customer'];
    $total = $_POST['total'];
    $discount = $_POST['discount'];
    $tax = $_POST['tax'];
    $grand_total = $_POST['grand_total'];
    $payment_method = $_POST['payment_method'];
    $products = json_decode($_POST['products'], true);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO sales (customer, total, discount, tax, grand_total, payment_method, sale_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sddsss", $customer, $total, $discount, $tax, $grand_total, $payment_method);
        $stmt->execute();
        $sale_id = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, rate) VALUES (?, ?, ?, ?)");
        foreach ($products as $product) {
            $stmt->bind_param("iiid", $sale_id, $product['id'], $product['quantity'], $product['rate']);
            $stmt->execute();

            $stmt2 = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt2->bind_param("ii", $product['quantity'], $product['id']);
            $stmt2->execute();
        }

        $conn->commit();
        redirect('index.php?page=invoice&id=' . $sale_id);
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Failed to create sale";
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">New Sale</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <div class="input-group">
                <input type="text" id="product_search" class="form-control" placeholder="Search for products by name, SKU, or barcode">
                <button class="btn btn-outline-secondary" type="button" id="scan_btn">Scan</button>
            </div>
        </div>
        <div id="scanner-container" style="display: none;">
            <video id="scanner" style="width: 100%;"></video>
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
            <tbody id="sale_items">
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <form method="post" id="sale_form">
            <input type="hidden" name="products" id="products_input">
            <div class="mb-3">
                <label for="customer" class="form-label">Customer</label>
                <input type="text" class="form-control" id="customer" name="customer">
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" class="form-control" id="total" name="total" readonly>
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Discount</label>
                <input type="number" class="form-control" id="discount" name="discount" value="0">
            </div>
            <div class="mb-3">
                <label for="tax" class="form-label">Tax</label>
                <input type="number" class="form-control" id="tax" name="tax" value="0">
            </div>
            <div class="mb-3">
                <label for="grand_total" class="form-label">Grand Total</label>
                <input type="number" class="form-control" id="grand_total" name="grand_total" readonly>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select class="form-select" id="payment_method" name="payment_method">
                    <option value="Cash">Cash</option>
                    <option value="UPI">UPI</option>
                    <option value="Card">Card</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create Sale</button>
        </form>
    </div>
</div>

<script>
    const productSearch = document.getElementById('product_search');
    const productSuggestions = document.getElementById('product_suggestions');
    const saleItems = document.getElementById('sale_items');
    const totalInput = document.getElementById('total');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const grandTotalInput = document.getElementById('grand_total');
    const productsInput = document.getElementById('products_input');
    const saleForm = document.getElementById('sale_form');

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
                rate: product.selling_rate,
                quantity: 1,
            });
        }
        renderSaleItems();
    }

    function removeProduct(productId) {
        products = products.filter(p => p.id !== productId);
        renderSaleItems();
    }

    function updateQuantity(productId, quantity) {
        const product = products.find(p => p.id === productId);
        if (product) {
            product.quantity = quantity;
        }
        renderSaleItems();
    }

    function renderSaleItems() {
        saleItems.innerHTML = '';
        let total = 0;
        products.forEach(product => {
            const tr = document.createElement('tr');
            const totalForRow = product.quantity * product.rate;
            total += totalForRow;
            tr.innerHTML = `
                <td>${product.name}</td>
                <td><input type="number" value="${product.quantity}" min="1" onchange="updateQuantity(${product.id}, this.value)"></td>
                <td>${product.rate}</td>
                <td>${totalForRow.toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeProduct(${product.id})">Remove</button></td>
            `;
            saleItems.appendChild(tr);
        });
        totalInput.value = total.toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        const total = parseFloat(totalInput.value);
        const discount = parseFloat(discountInput.value);
        const tax = parseFloat(taxInput.value);
        const grandTotal = total - discount + tax;
        grandTotalInput.value = grandTotal.toFixed(2);
    }

    discountInput.addEventListener('input', updateGrandTotal);
    taxInput.addEventListener('input', updateGrandTotal);

    saleForm.addEventListener('submit', (e) => {
        productsInput.value = JSON.stringify(products);
    });

    const scanBtn = document.getElementById('scan_btn');
    const scannerContainer = document.getElementById('scanner-container');
    const scanner = document.getElementById('scanner');
    let scanning = false;

    scanBtn.addEventListener('click', () => {
        if (scanning) {
            Quagga.stop();
            scannerContainer.style.display = 'none';
            scanning = false;
        } else {
            scannerContainer.style.display = 'block';
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: scanner,
                    constraints: {
                        width: 480,
                        height: 320,
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader", "codabar_reader", "upc_reader", "upc_e_reader", "i2of5_reader"]
                },
            }, function (err) {
                if (err) {
                    console.log(err);
                    return
                }
                Quagga.start();
                scanning = true;
            });
        }
    });

    Quagga.onDetected((data) => {
        const barcode = data.codeResult.code;
        productSearch.value = barcode;
        productSearch.dispatchEvent(new Event('input'));
        Quagga.stop();
        scannerContainer.style.display = 'none';
        scanning = false;
    });
</script>
