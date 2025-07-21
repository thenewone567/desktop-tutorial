<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = get_db_connection();
    $username = $_POST['username'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $password = $user['password'];
    } else {
        $error = "User not found";
    }
}
?>

<?php include 'header.php'; ?>

<h1>Forgot Password</h1>

<?php if (isset($password)): ?>
    <div class="alert alert-success">Your password is: <?php echo $password; ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" action="index.php?page=forgot_password">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <a href="index.php?page=login" class="btn btn-secondary">Cancel</a>
</form>

<?php include 'footer.php'; ?>
