<?php
if (!has_permission($_SESSION['role'], 'manage_sales')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$customer_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $credit_limit = $_POST['credit_limit'];

    $stmt = $conn->prepare("UPDATE customers SET name = ?, phone = ?, email = ?, address = ?, credit_limit = ? WHERE id = ?");
    $stmt->bind_param("ssssdi", $name, $phone, $email, $address, $credit_limit, $customer_id);

    if ($stmt->execute()) {
        log_activity($_SESSION['user_id'], "Updated customer: $name");
        redirect('index.php?page=customers');
    } else {
        $error = "Failed to update customer";
    }
}

$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Customer</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label for="name" class="form-label">Customer Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $customer['name']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $customer['phone']; ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $customer['email']; ?>">
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address"><?php echo $customer['address']; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="credit_limit" class="form-label">Credit Limit</label>
        <input type="number" class="form-control" id="credit_limit" name="credit_limit" step="0.01" value="<?php echo $customer['credit_limit']; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update Customer</button>
</form>
