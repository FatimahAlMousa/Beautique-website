<?php
$conn = new mysqli("localhost", "root", "", "beautique_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection failed.";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "Invalid product ID.";
    exit;
}

$id = intval($_GET['id']);
$conn->query("DELETE FROM products WHERE id = $id");

if ($conn->affected_rows > 0) {
    echo "Product deleted.";
} else {
    echo "Product not found or couldn't be deleted.";
}
?>
