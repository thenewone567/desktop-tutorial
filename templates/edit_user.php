<?php
if (!has_permission($_SESSION['role'], 'manage_users')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$user_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $password, $role, $status, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $role, $status, $user_id);
    }

    if ($stmt->execute()) {
        log_activity($_SESSION['user_id'], "Updated user: $username");
        redirect('index.php?page=users');
    } else {
        $error = "Failed to update user";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit User</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password (leave blank to keep current password)</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-select" id="role" name="role">
            <option value="Admin" <?php if ($user['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
            <option value="Manager" <?php if ($user['role'] === 'Manager') echo 'selected'; ?>>Manager</option>
            <option value="Supervisor" <?php if ($user['role'] === 'Supervisor') echo 'selected'; ?>>Supervisor</option>
            <option value="Warehouse Associate" <?php if ($user['role'] === 'Warehouse Associate') echo 'selected'; ?>>Warehouse Associate</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="Active" <?php if ($user['status'] === 'Active') echo 'selected'; ?>>Active</option>
            <option value="Inactive" <?php if ($user['status'] === 'Inactive') echo 'selected'; ?>>Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update User</button>
</form>
