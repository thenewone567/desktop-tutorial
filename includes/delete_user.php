<?php
if (!has_permission($_SESSION['role'], 'manage_users')) {
    redirect('index.php?page=dashboard');
}

$conn = get_db_connection();
$user_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    log_activity($_SESSION['user_id'], "Deleted user with id: $user_id");
    redirect('index.php?page=users');
} else {
    $error = "Failed to delete user";
    redirect('index.php?page=users&error=' . urlencode($error));
}
