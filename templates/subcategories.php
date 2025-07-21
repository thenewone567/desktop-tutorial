<?php
if (!has_permission($_SESSION['role'], 'manage_products')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $stmt = $conn->prepare("INSERT INTO subcategories (name, category_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $category_id);
    $stmt->execute();
    log_activity($_SESSION['user_id'], "Added new subcategory: $name");
    redirect('index.php?page=subcategories');
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM subcategories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    log_activity($_SESSION['user_id'], "Deleted subcategory with id: $id");
    redirect('index.php?page=subcategories');
}

$result = $conn->query("
    SELECT s.*, c.name as category_name
    FROM subcategories s
    LEFT JOIN categories c ON s.category_id = c.id
");
$categories = $conn->query("SELECT * FROM categories");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Subcategories</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Subcategory Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Subcategory</button>
        </form>
    </div>
    <div class="col-md-8">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td>
                            <a href="index.php?page=subcategories&delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this subcategory?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
