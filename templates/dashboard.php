<?php
$conn = get_db_connection();

// Sales Overview
$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$month_start = date('Y-m-d', strtotime('first day of this month'));

$today_sales = $conn->query("SELECT SUM(grand_total) as total FROM sales WHERE sale_date = '$today'")->fetch_assoc()['total'] ?? 0;
$week_sales = $conn->query("SELECT SUM(grand_total) as total FROM sales WHERE sale_date >= '$week_start'")->fetch_assoc()['total'] ?? 0;
$month_sales = $conn->query("SELECT SUM(grand_total) as total FROM sales WHERE sale_date >= '$month_start'")->fetch_assoc()['total'] ?? 0;

// Top Selling Items
$top_selling_items = $conn->query("
    SELECT p.name, SUM(si.quantity) as total_quantity
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    GROUP BY si.product_id
    ORDER BY total_quantity DESC
    LIMIT 5
");

// Low Stock Alerts
$low_stock_items = $conn->query("SELECT * FROM products WHERE quantity <= 10");

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=add_sale" class="btn btn-sm btn-outline-secondary">
            New Sale
        </a>
        <a href="index.php?page=add_purchase" class="btn btn-sm btn-outline-secondary">
            New Purchase
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Sales Overview
            </div>
            <div class="card-body">
                <p>Today: ₹<?php echo number_format($today_sales, 2); ?></p>
                <p>This Week: ₹<?php echo number_format($week_sales, 2); ?></p>
                <p>This Month: ₹<?php echo number_format($month_sales, 2); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Top Selling Items
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php while ($item = $top_selling_items->fetch_assoc()): ?>
                        <li class="list-group-item"><?php echo $item['name']; ?> (<?php echo $item['total_quantity']; ?>)</li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Low Stock Alerts
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php while ($item = $low_stock_items->fetch_assoc()): ?>
                        <li class="list-group-item"><?php echo $item['name']; ?> (<?php echo $item['quantity']; ?>)</li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
