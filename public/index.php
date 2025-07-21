<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

$page = $_GET['page'] ?? 'login';

if (!is_logged_in() && $page !== 'login') {
    redirect('index.php?page=login');
}

switch ($page) {
    case 'login':
        include '../templates/login.php';
        break;
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
    case 'forgot_password':
        include '../templates/forgot_password.php';
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
    case 'logout':
        logout();
        redirect('index.php?page=login');
        break;
    default:
        include '../templates/dashboard.php';
        break;
}
