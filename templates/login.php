<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = get_db_connection();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Uncomment the following lines for debugging
    var_dump($user);
    var_dump($password);
    var_dump($user['password']);

    if ($user && $password === $user['password']) { // In a real application, use password_verify()
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Uncomment the following line for debugging
        var_dump($_SESSION);

        redirect('index.php?page=dashboard');
    } else {
        $error = "Invalid username or password";
    }
}
?>

<?php include 'header.php'; ?>

<h1>Login</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
    <a href="index.php?page=forgot_password" class="btn btn-link">Forgot Password?</a>
</form>

<?php include 'footer.php'; ?>
