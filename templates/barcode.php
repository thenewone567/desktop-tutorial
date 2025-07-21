<?php
if (!has_permission($_SESSION['role'], 'manage_products')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$products = $conn->query("SELECT id, name, barcode FROM products");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Barcode Generator</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <form id="barcode_form">
            <div class="mb-3">
                <label for="product_id" class="form-label">Product</label>
                <select class="form-select" id="product_id" name="product_id" required>
                    <option value="">Select Product</option>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <option value="<?php echo $product['id']; ?>" data-barcode="<?php echo $product['barcode']; ?>"><?php echo $product['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generate Barcode</button>
        </form>
    </div>
    <div class="col-md-8">
        <div id="barcode_container"></div>
        <button id="print_btn" class="btn btn-secondary mt-3" style="display:none;">Print Barcode</button>
    </div>
</div>

<script src="js/quagga.min.js"></script>
<script>
    const barcodeForm = document.getElementById('barcode_form');
    const productIdSelect = document.getElementById('product_id');
    const barcodeContainer = document.getElementById('barcode_container');
    const printBtn = document.getElementById('print_btn');

    barcodeForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const selectedOption = productIdSelect.options[productIdSelect.selectedIndex];
        const barcode = selectedOption.dataset.barcode;
        if (barcode) {
            generateBarcode(barcode);
        }
    });

    function generateBarcode(barcode) {
        barcodeContainer.innerHTML = '';
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute('jsbarcode-format', 'code128');
        svg.setAttribute('jsbarcode-value', barcode);
        svg.setAttribute('jsbarcode-textmargin', '0');
        svg.setAttribute('jsbarcode-fontoptions', 'bold');
        barcodeContainer.appendChild(svg);
        JsBarcode(svg).init();
        printBtn.style.display = 'block';
    }

    printBtn.addEventListener('click', () => {
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print Barcode</title></head><body>');
        printWindow.document.write(barcodeContainer.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
</script>
