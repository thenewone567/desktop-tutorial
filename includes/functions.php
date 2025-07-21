<?php
function redirect($url) {
    header("Location: $url");
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function login($username, $password) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND status = 'Active'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            log_activity($user['id'], 'Logged in');
            return true;
        }
    }
    return false;
}

function logout() {
    log_activity($_SESSION['user_id'], 'Logged out');
    session_unset();
    session_destroy();
}

function has_permission($role, $permission) {
    $permissions = [
        'Admin' => ['manage_users', 'manage_products', 'manage_sales', 'manage_purchases', 'view_reports'],
        'Manager' => ['manage_products', 'manage_sales', 'manage_purchases', 'view_reports'],
        'Supervisor' => ['manage_products', 'manage_sales', 'manage_purchases'],
        'Warehouse Associate' => ['manage_products']
    ];
    return in_array($permission, $permissions[$role]);
}

function log_activity($user_id, $activity) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $activity);
    $stmt->execute();
}
