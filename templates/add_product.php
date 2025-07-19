<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = get_db_connection();
    $name = $_POST['name'];
    $sku = $_POST['sku'];
    $barcode = $_POST['barcode'];
    $category = $_POST['category'];
    $purchase_rate = $_POST['purchase_rate'];
    $selling_rate = $_POST['selling_rate'];
    $quantity = $_POST['quantity'];
    $aisle = $_POST['aisle'];
    $rack = $_POST['rack'];
    $bin = $_POST['bin'];

    $sql = "INSERT INTO products (name, sku, barcode, category, purchase_rate, selling_rate, quantity, aisle, rack, bin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssddisss", $name, $sku, $barcode, $category, $purchase_rate, $selling_rate, $quantity, $aisle, $rack, $bin);
    $stmt->execute();

    redirect('index.php?page=inventory');
}
?>

<?php include 'header.php'; ?>

<h1>Add Product</h1>

<form method="post">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" class="form-control" id="sku" name="sku" required>
    </div>
    <div class="mb-3">
        <label for="barcode" class="form-label">Barcode</label>
        <div class="input-group">
            <input type="text" class="form-control" id="barcode" name="barcode" required>
            <button class="btn btn-outline-secondary" type="button" id="scan-barcode">Scan</button>
        </div>
    </div>
    <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <input type="text" class="form-control" id="category" name="category" required>
    </div>
    <div class="mb-3">
        <label for="purchase_rate" class="form-label">Purchase Rate</label>
        <input type="number" class="form-control" id="purchase_rate" name="purchase_rate" step="0.01" required>
    </div>
    <div class="mb-3">
        <label for="selling_rate" class="form-label">Selling Rate</label>
        <input type="number" class="form-control" id="selling_rate" name="selling_rate" step="0.01" required>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" id="quantity" name="quantity" required>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label for="aisle" class="form-label">Aisle</label>
            <input type="text" class="form-control" id="aisle" name="aisle" required>
        </div>
        <div class="col-md-4">
            <label for="rack" class="form-label">Rack</label>
            <input type="text" class="form-control" id="rack" name="rack" required>
        </div>
        <div class="col-md-4">
            <label for="bin" class="form-label">Bin</label>
            <input type="text" class="form-control" id="bin" name="bin" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Add Product</button>
    <a href="index.php?page=inventory" class="btn btn-secondary mt-3">Cancel</a>
</form>

<div class="modal" id="barcode-scanner-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="interactive" class="viewport"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="js/quagga.min.js"></script>
<script>
    const scannerModal = new bootstrap.Modal(document.getElementById('barcode-scanner-modal'));

    document.getElementById('scan-barcode').addEventListener('click', function() {
        scannerModal.show();
        Quagga.init({
            inputStream : {
                name : "Live",
                type : "LiveStream",
                target: document.querySelector('#interactive')
            },
            decoder : {
                readers : ["code_128_reader"]
            }
        }, function(err) {
            if (err) {
                console.log(err);
                return
            }
            Quagga.start();
        });
    });

    Quagga.onDetected(function(result) {
        document.getElementById('barcode').value = result.codeResult.code;
        Quagga.stop();
        scannerModal.hide();
    });

    document.getElementById('barcode-scanner-modal').addEventListener('hidden.bs.modal', function () {
        Quagga.stop();
    });
</script>

<?php include 'footer.php'; ?>
