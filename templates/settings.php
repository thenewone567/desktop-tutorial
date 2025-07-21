<?php
if (!has_permission($_SESSION['role'], 'Admin')) {
    redirect('index.php?page=dashboard');
}

$settings_file = '../config/settings.json';
$settings = json_decode(file_get_contents($settings_file), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['company_name'] = $_POST['company_name'];
    $settings['company_logo'] = $_POST['company_logo'];
    $settings['company_gst'] = $_POST['company_gst'];
    $settings['company_address'] = $_POST['company_address'];
    $settings['company_phone'] = $_POST['company_phone'];
    $settings['company_email'] = $_POST['company_email'];
    $settings['currency'] = $_POST['currency'];
    $settings['invoice_footer'] = $_POST['invoice_footer'];
    $settings['invoice_signature'] = $_POST['invoice_signature'];

    file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));
    log_activity($_SESSION['user_id'], "Updated settings");
    redirect('index.php?page=settings');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Settings</h1>
</div>

<form method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $settings['company_name']; ?>">
            </div>
            <div class="mb-3">
                <label for="company_logo" class="form-label">Company Logo URL</label>
                <input type="text" class="form-control" id="company_logo" name="company_logo" value="<?php echo $settings['company_logo']; ?>">
            </div>
            <div class="mb-3">
                <label for="company_gst" class="form-label">GSTIN</label>
                <input type="text" class="form-control" id="company_gst" name="company_gst" value="<?php echo $settings['company_gst']; ?>">
            </div>
            <div class="mb-3">
                <label for="company_address" class="form-label">Company Address</label>
                <textarea class="form-control" id="company_address" name="company_address"><?php echo $settings['company_address']; ?></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="company_phone" class="form-label">Company Phone</label>
                <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?php echo $settings['company_phone']; ?>">
            </div>
            <div class="mb-3">
                <label for="company_email" class="form-label">Company Email</label>
                <input type="email" class="form-control" id="company_email" name="company_email" value="<?php echo $settings['company_email']; ?>">
            </div>
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <input type="text" class="form-control" id="currency" name="currency" value="<?php echo $settings['currency']; ?>">
            </div>
            <div class="mb-3">
                <label for="invoice_footer" class="form-label">Invoice Footer</label>
                <input type="text" class="form-control" id="invoice_footer" name="invoice_footer" value="<?php echo $settings['invoice_footer']; ?>">
            </div>
            <div class="mb-3">
                <label for="invoice_signature" class="form-label">Invoice Signature URL</label>
                <input type="text" class="form-control" id="invoice_signature" name="invoice_signature" value="<?php echo $settings['invoice_signature']; ?>">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
