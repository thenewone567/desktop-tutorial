<?php
if (!has_permission($_SESSION['role'], 'manage_products')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

// Search and filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "
    SELECT p.*, c.name as category_name, w.name as warehouse_location_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN warehouse_locations w ON p.warehouse_location_id = w.id
    WHERE (p.name LIKE ? OR p.sku LIKE ?)
";
if ($category) {
    $sql .= " AND p.category_id = ?";
}

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
if ($category) {
    $stmt->bind_param("ssi", $search_param, $search_param, $category);
} else {
    $stmt->bind_param("ss", $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();

$categories = $conn->query("SELECT * FROM categories");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Inventory</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=add_product" class="btn btn-sm btn-outline-secondary">
            Add Product
        </a>
    </div>
</div>

<form class="row g-3 mb-3">
    <div class="col-auto">
        <input type="text" class="form-control" name="search" placeholder="Search by name or SKU" value="<?php echo $search; ?>">
    </div>
    <div class="col-auto">
        <select class="form-select" name="category">
            <option value="">All Categories</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $category) echo 'selected'; ?>><?php echo $cat['name']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Warehouse Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['sku']; ?></td>
                    <td><?php echo $row['category_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['warehouse_location_name']; ?></td>
                    <td>
                        <a href="index.php?page=edit_product&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="index.php?page=delete_product&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
