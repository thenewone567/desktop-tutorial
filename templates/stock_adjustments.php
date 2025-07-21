<?php
if (!has_permission($_SESSION['role'], 'manage_products')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $adjustment = $_POST['adjustment'];
    $reason = $_POST['reason'];
    $user_id = $_SESSION['user_id'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO stock_adjustments (product_id, user_id, adjustment, reason) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $product_id, $user_id, $adjustment, $reason);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        $stmt->bind_param("ii", $adjustment, $product_id);
        $stmt->execute();

        $conn->commit();
        log_activity($_SESSION['user_id'], "Adjusted stock for product id: $product_id by $adjustment");
        redirect('index.php?page=stock_adjustments');
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Failed to adjust stock";
    }
}

$products = $conn->query("SELECT id, name FROM products");
$adjustments = $conn->query("
    SELECT sa.*, p.name as product_name, u.username as user_name
    FROM stock_adjustments sa
    JOIN products p ON sa.product_id = p.id
    JOIN users u ON sa.user_id = u.id
    ORDER BY sa.date DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stock Adjustments</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <form method="post">
            <div class="mb-3">
                <label for="product_id" class="form-label">Product</label>
                <select class="form-select" id="product_id" name="product_id" required>
                    <option value="">Select Product</option>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="adjustment" class="form-label">Adjustment</label>
                <input type="number" class="form-control" id="adjustment" name="adjustment" required>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
                <input type="text" class="form-control" id="reason" name="reason">
            </div>
            <button type="submit" class="btn btn-primary">Adjust Stock</button>
        </form>
    </div>
    <div class="col-md-8">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>User</th>
                    <th>Adjustment</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $adjustments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td><?php echo $row['adjustment']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
