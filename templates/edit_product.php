<?php
if (!has_permission($_SESSION['role'], 'manage_products')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$product_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $sku = $_POST['sku'];
    $barcode = $_POST['barcode'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $purchase_rate = $_POST['purchase_rate'];
    $selling_rate = $_POST['selling_rate'];
    $quantity = $_POST['quantity'];
    $min_stock = $_POST['min_stock'];
    $max_stock = $_POST['max_stock'];
    $warehouse_location_id = $_POST['warehouse_location_id'];
    $batch_number = $_POST['batch_number'];
    $expiry_date = $_POST['expiry_date'];
    $image = $_POST['current_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $conn->prepare("
        UPDATE products SET
        name = ?, sku = ?, barcode = ?, category_id = ?, subcategory_id = ?, purchase_rate = ?, selling_rate = ?, quantity = ?, min_stock = ?, max_stock = ?, warehouse_location_id = ?, batch_number = ?, expiry_date = ?, image = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssiisdiisssssi", $name, $sku, $barcode, $category_id, $subcategory_id, $purchase_rate, $selling_rate, $quantity, $min_stock, $max_stock, $warehouse_location_id, $batch_number, $expiry_date, $image, $product_id);

    if ($stmt->execute()) {
        log_activity($_SESSION['user_id'], "Updated product: $name");
        redirect('index.php?page=inventory');
    } else {
        $error = "Failed to update product";
    }
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

$categories = $conn->query("SELECT * FROM categories");
$subcategories = $conn->query("SELECT * FROM subcategories");
$warehouse_locations = $conn->query("SELECT * FROM warehouse_locations");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Product</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="current_image" value="<?php echo $product['image']; ?>">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $product['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" class="form-control" id="sku" name="sku" value="<?php echo $product['sku']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="barcode" class="form-label">Barcode</label>
                <input type="text" class="form-control" id="barcode" name="barcode" value="<?php echo $product['barcode']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>><?php echo $cat['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subcategory_id" class="form-label">Subcategory</label>
                <select class="form-select" id="subcategory_id" name="subcategory_id">
                    <option value="">Select Subcategory</option>
                    <?php while ($subcat = $subcategories->fetch_assoc()): ?>
                        <option value="<?php echo $subcat['id']; ?>" <?php if ($subcat['id'] == $product['subcategory_id']) echo 'selected'; ?>><?php echo $subcat['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="purchase_rate" class="form-label">Purchase Rate</label>
                <input type="number" class="form-control" id="purchase_rate" name="purchase_rate" step="0.01" value="<?php echo $product['purchase_rate']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="selling_rate" class="form-label">Selling Rate</label>
                <input type="number" class="form-control" id="selling_rate" name="selling_rate" step="0.01" value="<?php echo $product['selling_rate']; ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $product['quantity']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="min_stock" class="form-label">Min Stock</label>
                <input type="number" class="form-control" id="min_stock" name="min_stock" value="<?php echo $product['min_stock']; ?>">
            </div>
            <div class="mb-3">
                <label for="max_stock" class="form-label">Max Stock</label>
                <input type="number" class="form-control" id="max_stock" name="max_stock" value="<?php echo $product['max_stock']; ?>">
            </div>
            <div class="mb-3">
                <label for="warehouse_location_id" class="form-label">Warehouse Location</label>
                <select class="form-select" id="warehouse_location_id" name="warehouse_location_id">
                    <option value="">Select Location</option>
                    <?php while ($loc = $warehouse_locations->fetch_assoc()): ?>
                        <option value="<?php echo $loc['id']; ?>" <?php if ($loc['id'] == $product['warehouse_location_id']) echo 'selected'; ?>><?php echo $loc['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="batch_number" class="form-label">Batch Number</label>
                <input type="text" class="form-control" id="batch_number" name="batch_number" value="<?php echo $product['batch_number']; ?>">
            </div>
            <div class="mb-3">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo $product['expiry_date']; ?>">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image">
                <?php if ($product['image']): ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail mt-2" width="100">
                <?php endif; ?>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
</form>
