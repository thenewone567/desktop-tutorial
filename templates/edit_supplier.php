<?php
if (!has_permission($_SESSION['role'], 'manage_purchases')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$supplier_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $gstin = $_POST['gstin'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE suppliers SET name = ?, gstin = ?, phone = ?, email = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $gstin, $phone, $email, $address, $supplier_id);

    if ($stmt->execute()) {
        log_activity($_SESSION['user_id'], "Updated supplier: $name");
        redirect('index.php?page=suppliers');
    } else {
        $error = "Failed to update supplier";
    }
}

$stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Supplier</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label for="name" class="form-label">Supplier Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $supplier['name']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="gstin" class="form-label">GSTIN</label>
        <input type="text" class="form-control" id="gstin" name="gstin" value="<?php echo $supplier['gstin']; ?>">
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $supplier['phone']; ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $supplier['email']; ?>">
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address"><?php echo $supplier['address']; ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Supplier</button>
</form>
