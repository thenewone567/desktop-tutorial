<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$return_type = $_GET['return_type'] ?? 'all';

$sql = "";
if ($return_type === 'sales' || $return_type === 'all') {
    $sql .= "
        SELECT 'Sales Return' as type, sr.id, sr.return_date as date, u.username as user_name
        FROM sales_returns sr
        LEFT JOIN users u ON sr.user_id = u.id
        WHERE sr.return_date BETWEEN ? AND ?
    ";
}
if ($return_type === 'all') {
    $sql .= " UNION ALL ";
}
if ($return_type === 'purchase' || $return_type === 'all') {
    $sql .= "
        SELECT 'Purchase Return' as type, pr.id, pr.return_date as date, u.username as user_name
        FROM purchase_returns pr
        LEFT JOIN users u ON pr.user_id = u.id
        WHERE pr.return_date BETWEEN ? AND ?
    ";
}
$sql .= " ORDER BY date DESC";

$stmt = $conn->prepare($sql);
if ($return_type === 'all') {
    $stmt->bind_param("ssss", $start_date, $end_date, $start_date, $end_date);
} else {
    $stmt->bind_param("ss", $start_date, $end_date);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Returns Report</h1>
</div>

<form class="row g-3 mb-3">
    <div class="col-auto">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
    </div>
    <div class="col-auto">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
    </div>
    <div class="col-auto">
        <label for="return_type" class="form-label">Return Type</label>
        <select class="form-select" id="return_type" name="return_type">
            <option value="all" <?php if ($return_type === 'all') echo 'selected'; ?>>All</option>
            <option value="sales" <?php if ($return_type === 'sales') echo 'selected'; ?>>Sales</option>
            <option value="purchase" <?php if ($return_type === 'purchase') echo 'selected'; ?>>Purchase</option>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Type</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
