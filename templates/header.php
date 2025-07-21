<!DOCTYPE html>
<html>
<head>
    <title>Hardware Shop & Warehouse Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body.dark-mode {
            background-color: #212529;
            color: #f8f9fa;
        }
        .dark-mode .card {
            background-color: #343a40;
            color: #f8f9fa;
        }
        .dark-mode .table {
            color: #f8f9fa;
        }
        .dark-mode .list-group-item {
            background-color: #343a40;
            color: #f8f9fa;
        }
    </style>
</head>
<body>
    <?php if (is_logged_in()): ?>
        <?php include 'navigation.php'; ?>
    <?php endif; ?>
    <div class="container">
