<?php
$conn = new mysqli("localhost", "root", "", "beautique_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $image = $conn->real_escape_string($_POST['image']);

    $sql = "UPDATE products SET
        name = '$name',
        description = '$description',
        price = $price,
        stock = $stock,
        category_id = $category_id,
        image = '$image'
        WHERE id = $id";

    if ($conn->query($sql)) {
        header("Location: adminProductManagement.php?query=" . urlencode($name));
        exit;
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>
