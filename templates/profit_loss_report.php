<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

$stmt = $conn->prepare("
    SELECT
        (SELECT SUM(grand_total) FROM sales WHERE sale_date BETWEEN ? AND ?) as total_revenue,
        (SELECT SUM(pi.quantity * pi.rate) FROM purchase_items pi JOIN purchases p ON pi.purchase_id = p.id WHERE p.purchase_date BETWEEN ? AND ?) as cost_of_goods_sold
");
$stmt->bind_param("ssss", $start_date, $end_date, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$total_revenue = $data['total_revenue'] ?? 0;
$cost_of_goods_sold = $data['cost_of_goods_sold'] ?? 0;
$gross_profit = $total_revenue - $cost_of_goods_sold;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Profit & Loss Report</h1>
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
                    <th>Total Revenue</th>
                    <td><?php echo number_format($total_revenue, 2); ?></td>
                </tr>
                <tr>
                    <th>Cost of Goods Sold</th>
                    <td><?php echo number_format($cost_of_goods_sold, 2); ?></td>
                </tr>
                <tr>
                    <th>Gross Profit</th>
                    <td><?php echo number_format($gross_profit, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
