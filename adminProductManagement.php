<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "beautique_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $desc = $conn->real_escape_string($_POST['description']);
    $image = $conn->real_escape_string($_POST['image']);
    $category = $conn->real_escape_string($_POST['category']);

    $sql = "INSERT INTO products (name, description, price, category_id, image) 
            VALUES ('$name', '$desc', $price, '$category', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Product added successfully!'); window.location.href='adminProductManagement.php';</script>";
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Product Management</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
  <link href="https://fonts.googleapis.com/css?family=Great+Vibes|Open+Sans:400,700&display=swap&subset=latin-ext" rel="stylesheet">
  <link rel="stylesheet" href="css/cart.css">
</head>
<body>
<div class="header_section">
  <div class="container-fluid">
    <nav class="navbar navbar-light bg-light justify-content-between">
      <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="homepage.php">Home</a>
        <a href="Contactus.php">Contact Us</a>
        <a href="adminAuthentication.php">Administrator's Authentication</a>
      </div>
      <span class="toggle_icon" onclick="openNav()"><img src="images/toggle-icon.png"></span>
      <a class="logo" href="Homepage.php"><img src="images/logo.png"></a>
      <form class="form-inline">
        <div class="login_text"></div>
      </form>
    </nav>
  </div>
</div>

<div class="container mt-5">
  <h1>Beauty Shop Admin Panel</h1>

  <h2>Add New Product</h2>
  <form method="post" action="adminProductManagement.php" onsubmit="return validateForm();">
    <input type="text" name="name" id="productName" placeholder="Product Name" class="form-control mb-2" required>
    <input type="text" name="price" id="productPrice" placeholder="Price (e.g. 19.99)" class="form-control mb-2" required>
    <textarea name="description" id="productDesc" placeholder="Brief Description" class="form-control mb-2" required></textarea>

    <select name="image" id="productImage" class="form-control mb-2" required>
      <option value="">Select Product Image</option>
      <option value="eyeshadow_palette.jpg">pallate</option>
      <option value="mascara_set.jpg">mascara</option>
      <option value="lipgloss1.jpg">lip gloss</option>
      <option value="cream1.jpg">cream</option>
      <option value="perfume1.jpg">perfume</option>
      <option value="blusher_set.jpg">blusher</option>
      <option value="brushes1.jpg">makeup brushes</option>
      <option value="hairserum1.jpg">hair serum</option>
      <option value="lipstick_set.jpg">lipstick set</option>
      <option value="lipstick1.jpg">lipstick</option>
      <option value="shampoo1.jpg">shampoo</option>
      <option value="setting_powder.jpg">setting powder</option>
    </select>

    <select name="category" id="productCategory" class="form-control mb-2" required>
      <option value="1">Mascara</option>
      <option value="2">Lipstick Sets</option>
      <option value="3">Beauty Brushes</option>
      <option value="4">Setting Powder</option>
      <option value="5">Blush</option>
      <option value="6">Eyeshadow</option>
      <option value="7">serum</option>
      <option value="hair serum">hair serum</option>
      <option value="shampoo">shampoo</option>
      <option value="perfume">perfume</option>
      <option value="cream">cream</option>
      <option value="lipstick">lipstick</option>
    </select>

    <button type="submit" name="add_product" class="btn btn-success mb-4">Add Product</button>
  </form>

  <h2>Search Product</h2>
  <select id="productIdDropdown" class="form-control mb-2">
    <option value="">Select Product ID</option>
    <?php
      $productResult = $conn->query("SELECT id, name FROM products ORDER BY id ASC");
      if ($productResult && $productResult->num_rows > 0) {
          while ($row = $productResult->fetch_assoc()) {
              echo "<option value='{$row['id']}'>{$row['id']} - " . htmlspecialchars($row['name']) . "</option>";
          }
      }
    ?>
  </select>

  <input type="text" id="searchId" placeholder="Or enter product ID" class="form-control mb-2">
  <button onclick="searchProduct()" class="btn btn-primary">Search Product</button>

  <h2 class="mt-4">Product List</h2>
  <div id="productList" class="row"></div>
</div>

<script>
function validateForm() {
    const name = document.getElementById('productName').value.trim();
    const price = document.getElementById('productPrice').value.trim();
    const desc = document.getElementById('productDesc').value.trim();
    const image = document.getElementById('productImage').value;
    const category = document.getElementById('productCategory').value;

    if (!name || !price || isNaN(price) || !desc || !category || !image) {
        alert('Please fill in all fields correctly!');
        return false;
    }
    return true;
}

function searchProduct() {
  const textInput = document.getElementById("searchId").value.trim();
  const dropdownInput = document.getElementById("productIdDropdown").value;
  const query = textInput || dropdownInput;

  const xhr = new XMLHttpRequest();
  xhr.open("GET", "search_product_by_id.php?query=" + encodeURIComponent(query), true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      document.getElementById("productList").innerHTML = xhr.responseText;
    } else {
      alert("Error loading product.");
    }
  };
  xhr.send();
}

function modifyProduct(id) {
    window.location.href = `modifyProduct.php?id=${id}`;
}

function deleteProduct(id) {
  if (!confirm("Are you sure you want to delete this product?")) return;

  const xhr = new XMLHttpRequest();
  xhr.open("GET", "delete_product.php?id=" + id, true);
  xhr.onload = function () {
    alert(this.responseText);
    searchProduct(); 
  };
  xhr.send();
}

function openNav() {
  document.getElementById("mySidenav").style.width = "100%";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}

document.addEventListener('DOMContentLoaded', function () {
  const urlParams = new URLSearchParams(window.location.search);
  const query = urlParams.get('query');
  searchProduct();
});
</script>

</body>
</html>
