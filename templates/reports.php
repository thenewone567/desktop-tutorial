<?php
if (!has_permission($_SESSION['role'], 'view_reports')) {
    redirect('index.php?page=dashboard');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reports</h1>
</div>

<div class="list-group">
    <a href="index.php?page=sales_report" class="list-group-item list-group-item-action">Sales Report</a>
    <a href="index.php?page=purchases_report" class="list-group-item list-group-item-action">Purchases Report</a>
    <a href="index.php?page=returns_report" class="list-group-item list-group-item-action">Returns Report</a>
    <a href="index.php?page=stock_report" class="list-group-item list-group-item-action">Stock Report</a>
    <a href="index.php?page=profit_loss_report" class="list-group-item list-group-item-action">Profit & Loss Report</a>
    <a href="index.php?page=inventory_valuation_report" class="list-group-item list-group-item-action">Inventory Valuation Report</a>
    <a href="index.php?page=item_performance_report" class="list-group-item list-group-item-action">Item Performance Report</a>
    <a href="index.php?page=tax_summary_report" class="list-group-item list-group-item-action">Tax Summary Report</a>
</div>
