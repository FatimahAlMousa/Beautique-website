<?php
$conn = new mysqli("localhost", "root", "", "beautique_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

$input = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

if ($input === '') {
    $sql = "SELECT * FROM products";  
} elseif (is_numeric($input)) {
    $sql = "SELECT * FROM products WHERE id = $input";
} else {
    $sql = "SELECT * FROM products WHERE name LIKE '%$input%'";
}


$result = $conn->query($sql);


$input = $conn->real_escape_string($_GET['query']);

if (is_numeric($input)) {
    $sql = "SELECT * FROM products WHERE id = $input";
} else {
    $sql = "SELECT * FROM products WHERE name LIKE '%$input%'";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $name = htmlspecialchars($row['name']);
        $desc = htmlspecialchars($row['description']);
        $price = number_format($row['price'], 2);
        $image = htmlspecialchars($row['image']);

        echo "
        <div class='col-md-3 mb-4'>
            <div class='card'>
                <img src='images/$image' class='card-img-top' alt='$name'>
                <div class='card-body'>
                    <h5 class='card-title'>$name</h5>
                    <p class='card-text'>$desc</p>
                    <p class='card-text'><strong>Price:</strong> $$price</p>
                    <button class='btn btn-warning btn-sm' onclick='modifyProduct($id)'>Modify</button>
                    <button class='btn btn-danger btn-sm' onclick='deleteProduct($id)'>Delete</button>
                </div>
            </div>
        </div>";
    }
} else {
    echo "<div class='col-md-12'><p>Product not found.</p></div>";
}
?>
