<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'dashboard':
        include '../templates/dashboard.php';
        break;
    case 'inventory':
        include '../templates/inventory.php';
        break;
    case 'add_product':
        include '../templates/add_product.php';
        break;
    case 'edit_product':
        include '../templates/edit_product.php';
        break;
    case 'delete_product':
        include '../includes/delete_product.php';
        break;
    case 'purchases':
        include '../templates/purchases.php';
        break;
    case 'add_purchase':
        include '../templates/add_purchase.php';
        break;
    case 'sales':
        include '../templates/sales.php';
        break;
    case 'add_sale':
        include '../templates/add_sale.php';
        break;
    case 'invoice':
        include '../templates/invoice.php';
        break;
    case 'sales_returns':
        include '../templates/sales_returns.php';
        break;
    case 'add_sales_return':
        include '../templates/add_sales_return.php';
        break;
    case 'purchase_returns':
        include '../templates/purchase_returns.php';
        break;
    case 'add_purchase_return':
        include '../templates/add_purchase_return.php';
        break;
    case 'reports':
        include '../templates/reports.php';
        break;
    case 'sales_report':
        include '../templates/sales_report.php';
        break;
    case 'purchases_report':
        include '../templates/purchases_report.php';
        break;
    case 'stock_report':
        include '../templates/stock_report.php';
        break;
    case 'returns_report':
        include '../templates/returns_report.php';
        break;
    case 'categories':
        include '../templates/categories.php';
        break;
    case 'subcategories':
        include '../templates/subcategories.php';
        break;
    case 'warehouse_locations':
        include '../templates/warehouse_locations.php';
        break;
    case 'stock_adjustments':
        include '../templates/stock_adjustments.php';
        break;
    case 'suppliers':
        include '../templates/suppliers.php';
        break;
    case 'purchase_details':
        include '../templates/purchase_details.php';
        break;
    case 'stock_ledger':
        include '../templates/stock_ledger.php';
        break;
    case 'edit_supplier':
        include '../templates/edit_supplier.php';
        break;
    case 'supplier_due_report':
        include '../templates/supplier_due_report.php';
        break;
    case 'customers':
        include '../templates/customers.php';
        break;
    case 'edit_customer':
        include '../templates/edit_customer.php';
        break;
    case 'profit_loss_report':
        include '../templates/profit_loss_report.php';
        break;
    case 'inventory_valuation_report':
        include '../templates/inventory_valuation_report.php';
        break;
    case 'item_performance_report':
        include '../templates/item_performance_report.php';
        break;
    case 'tax_summary_report':
        include '../templates/tax_summary_report.php';
        break;
    case 'barcode':
        include '../templates/barcode.php';
        break;
    case 'settings':
        include '../templates/settings.php';
        break;
    case 'activity_log':
        include '../templates/activity_log.php';
        break;
    default:
        include '../templates/dashboard.php';
        break;
}
