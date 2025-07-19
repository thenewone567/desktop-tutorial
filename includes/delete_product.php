<?php
$conn = get_db_connection();
$id = $_GET['id'];

$sql = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

redirect('index.php?page=inventory');
