<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();

if (!is_logged_in()) {
    http_response_code(401);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'search_products':
        $query = $_GET['query'] ?? '';
        if (strlen($query) > 2) {
            $conn = get_db_connection();
            $stmt = $conn->prepare("SELECT id, name, sku, selling_rate FROM products WHERE name LIKE ? OR sku LIKE ? OR barcode LIKE ?");
            $search_param = "%$query%";
            $stmt->bind_param("sss", $search_param, $search_param, $search_param);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($products);
        }
        break;
    case 'get_sale_items':
        $sale_id = $_GET['sale_id'] ?? 0;
        if ($sale_id) {
            $conn = get_db_connection();
            $stmt = $conn->prepare("
                SELECT si.*, p.name as product_name
                FROM sale_items si
                LEFT JOIN products p ON si.product_id = p.id
                WHERE si.sale_id = ?
            ");
            $stmt->bind_param("i", $sale_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($items);
        }
        break;
    case 'get_purchase_items':
        $purchase_id = $_GET['purchase_id'] ?? 0;
        if ($purchase_id) {
            $conn = get_db_connection();
            $stmt = $conn->prepare("
                SELECT pi.*, p.name as product_name
                FROM purchase_items pi
                LEFT JOIN products p ON pi.product_id = p.id
                WHERE pi.purchase_id = ?
            ");
            $stmt->bind_param("i", $purchase_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($items);
        }
        break;
    default:
        http_response_code(404);
        break;
}
