<?php
$conn = get_db_connection();

// Monthly sales data
$monthly_sales_sql = "SELECT MONTH(sale_date) as month, SUM(grand_total) as total FROM sales GROUP BY MONTH(sale_date)";
$monthly_sales_result = $conn->query($monthly_sales_sql);
$monthly_sales_data = array_fill(0, 12, 0);
while ($row = $monthly_sales_result->fetch_assoc()) {
    $monthly_sales_data[$row['month'] - 1] = $row['total'];
}

// Top selling products data
$top_products_sql = "SELECT p.name, SUM(si.quantity) as total_quantity FROM sale_items si JOIN products p ON si.product_id = p.id GROUP BY si.product_id ORDER BY total_quantity DESC LIMIT 5";
$top_products_result = $conn->query($top_products_sql);
$top_products_labels = [];
$top_products_data = [];
while ($row = $top_products_result->fetch_assoc()) {
    $top_products_labels[] = $row['name'];
    $top_products_data[] = $row['total_quantity'];
}

// Stock levels data
$stock_sql = "SELECT name, quantity FROM products";
$stock_result = $conn->query($stock_sql);
$stock_labels = [];
$stock_data = [];
while ($row = $stock_result->fetch_assoc()) {
    $stock_labels[] = $row['name'];
    $stock_data[] = $row['quantity'];
}
?>

<?php include 'header.php'; ?>

<h1>Dashboard</h1>

<div class="row">
    <div class="col-md-6">
        <canvas id="monthly-sales-chart"></canvas>
    </div>
    <div class="col-md-6">
        <canvas id="top-products-chart"></canvas>
    </div>
</div>
<div class="row mt-5">
    <div class="col-md-12">
        <canvas id="stock-levels-chart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Sales Chart
    var ctx = document.getElementById('monthly-sales-chart').getContext('2d');
    var monthlySalesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Monthly Sales',
                data: <?php echo json_encode($monthly_sales_data); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Top Products Chart
    var ctx2 = document.getElementById('top-products-chart').getContext('2d');
    var topProductsChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($top_products_labels); ?>,
            datasets: [{
                label: 'Top Selling Products',
                data: <?php echo json_encode($top_products_data); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        }
    });

    // Stock Levels Chart
    var ctx3 = document.getElementById('stock-levels-chart').getContext('2d');
    var stockLevelsChart = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($stock_labels); ?>,
            datasets: [{
                label: 'Stock Levels',
                data: <?php echo json_encode($stock_data); ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include 'footer.php'; ?>
