<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

$stmt = $conn->prepare("
    SELECT s.*, u.username as user_name, c.name as customer_name
    FROM sales s
    LEFT JOIN users u ON s.user_id = u.id
    LEFT JOIN customers c ON s.customer_id = c.id
    WHERE s.sale_date BETWEEN ? AND ?
    ORDER BY s.sale_date DESC
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales Report</h1>
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
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>User</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['sale_date']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                    <td><?php echo $row['grand_total']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
