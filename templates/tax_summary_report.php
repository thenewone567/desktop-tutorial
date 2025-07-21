<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

$stmt = $conn->prepare("
    SELECT SUM(tax) as total_tax
    FROM sales
    WHERE sale_date BETWEEN ? AND ?
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$total_tax = $data['total_tax'] ?? 0;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tax Summary Report</h1>
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

<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Total Tax Collected</th>
                    <td><?php echo number_format($total_tax, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
