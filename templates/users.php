<?php
if (!has_permission($_SESSION['role'], 'manage_users')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$result = $conn->query("SELECT * FROM users");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Users</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=add_user" class="btn btn-sm btn-outline-secondary">
            Add User
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['last_login']; ?></td>
                    <td>
                        <a href="index.php?page=edit_user&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="index.php?page=delete_user&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
