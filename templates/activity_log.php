<?php
if (!has_permission($_SESSION['role'], 'Admin')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$result = $conn->query("
    SELECT al.*, u.username
    FROM activity_log al
    JOIN users u ON al.user_id = u.id
    ORDER BY al.date DESC
");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Activity Log</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Activity</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['activity']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
