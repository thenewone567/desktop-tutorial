<?php
if (!has_permission($_SESSION['role'], 'manage_sales')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $credit_limit = $_POST['credit_limit'];
    $stmt = $conn->prepare("INSERT INTO customers (name, phone, email, address, credit_limit) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $name, $phone, $email, $address, $credit_limit);
    $stmt->execute();
    log_activity($_SESSION['user_id'], "Added new customer: $name");
    redirect('index.php?page=customers');
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    log_activity($_SESSION['user_id'], "Deleted customer with id: $id");
    redirect('index.php?page=customers');
}

$result = $conn->query("SELECT * FROM customers");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Customers</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address"></textarea>
            </div>
            <div class="mb-3">
                <label for="credit_limit" class="form-label">Credit Limit</label>
                <input type="number" class="form-control" id="credit_limit" name="credit_limit" step="0.01">
            </div>
            <button type="submit" class="btn btn-primary">Add Customer</button>
        </form>
    </div>
    <div class="col-md-8">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Credit Limit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['credit_limit']; ?></td>
                        <td>
                            <a href="index.php?page=edit_customer&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="index.php?page=customers&delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
