<?php
$conn = get_db_connection();
$sale_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT s.*, c.name as customer_name, c.address as customer_address
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
$sale = $result->fetch_assoc();

$stmt = $conn->prepare("
    SELECT si.*, p.name as product_name
    FROM sale_items si
    LEFT JOIN products p ON si.product_id = p.id
    WHERE si.sale_id = ?
");
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$items = $stmt->get_result();

$settings_file = '../config/settings.json';
$settings = json_decode(file_get_contents($settings_file), true);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Invoice #<?php echo str_pad($sale['id'], 6, '0', STR_PAD_LEFT); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            Print
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-sm-6">
                <img src="<?php echo $settings['company_logo']; ?>" alt="Company Logo" width="150">
                <h6 class="mb-3"><?php echo $settings['company_name']; ?></h6>
                <div><?php echo $settings['company_address']; ?></div>
                <div>Email: <?php echo $settings['company_email']; ?></div>
                <div>Phone: <?php echo $settings['company_phone']; ?></div>
                <div>GSTIN: <?php echo $settings['company_gst']; ?></div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h4 class="mb-1">Invoice #<?php echo str_pad($sale['id'], 6, '0', STR_PAD_LEFT); ?></h4>
                <div>Date: <?php echo $sale['sale_date']; ?></div>
                <div class="mt-4">
                    <strong>Bill To:</strong>
                    <div><?php echo $sale['customer_name']; ?></div>
                    <div><?php echo $sale['customer_address']; ?></div>
                </div>
                <div class="mt-4">
                    <strong>Cashier:</strong>
                    <div><?php echo "N/A"; ?></div>
                </div>
            </div>
        </div>

        <div class="table-responsive-sm">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="center">#</th>
                        <th>Item</th>
                        <th class="right">Unit Cost</th>
                        <th class="center">Qty</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td class="center"><?php echo $i++; ?></td>
                            <td class="left strong"><?php echo $item['product_name']; ?></td>
                            <td class="right"><?php echo $item['rate']; ?></td>
                            <td class="center"><?php echo $item['quantity']; ?></td>
                            <td class="right"><?php echo $item['quantity'] * $item['rate']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-5">
            </div>
            <div class="col-lg-4 col-sm-5 ms-auto">
                <table class="table table-clear">
                    <tbody>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">Subtotal</strong>
                            </td>
                            <td class="right"><?php echo $sale['total']; ?></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">Discount</strong>
                            </td>
                            <td class="right"><?php echo $sale['discount']; ?></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">Tax</strong>
                            </td>
                            <td class="right"><?php echo $sale['tax']; ?></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">Total</strong>
                            </td>
                            <td class="right">
                                <strong class="text-dark"><?php echo $sale['grand_total']; ?></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <img src="<?php echo $settings['invoice_signature']; ?>" alt="Signature" width="150">
            </div>
            <div class="col-sm-6 text-sm-end">
                <p><?php echo $settings['invoice_footer']; ?></p>
            </div>
        </div>
    </div>
</div>
