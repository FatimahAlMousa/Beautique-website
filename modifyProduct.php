<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Modify Product</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product Details</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
  <link href="https://fonts.googleapis.com/css?family=Great+Vibes|Open+Sans:400,700&display=swap&subset=latin-ext" rel="stylesheet">
</head>
<body>
    <div class="header_section">
        <div class="container-fluid">
           <nav class="navbar navbar-light bg-light justify-content-between">
              <div id="mySidenav" class="sidenav">
                 <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                 <a href="homepage.php">Home</a>
                 <a href="products.php">Products</a>
                 <a href="contact.php">Contact Us</a>
                 <a href="adminAuthentication.php">Administrator's Authentication</a>
              </div>
              <span class="toggle_icon" onclick="openNav()"><img src="images/toggle-icon.png"></span>
              <a class="logo" href="homepage.php"><img src="images/logo.png"></a>
              <form class="form-inline">
                 <div class="login_text">
                    
                 </div>
              </form>
           </nav>
        </div>
     </div>
<div class="container mt-5">
  <h1>Modify or Delete Product</h1>
  <?php
  $conn = new mysqli("localhost", "root", "", "beautique_db");
  if ($conn->connect_error) die("Connection failed");
  
  $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
  $result = $conn->query("SELECT * FROM products WHERE id = $id");
  $product = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
  ?>
  <?php
  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
  $id = intval($_POST['id']);
  $conn->query("DELETE FROM products WHERE id = $id");
  echo "<script>alert('Product deleted successfully!'); window.location.href='adminProductManagement.php';</script>";
  exit;
}

  ?>

  <div class="container mt-5">
    <h1>Modify Product</h1>
  
    <?php if ($product): ?>
      <form method="POST" action="modify_product.php">

      <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
  
      <label>Product Name</label>
      <input type="text" name="name" class="form-control mb-2" value="<?php echo htmlspecialchars($product['name']); ?>">
  
      <label>Price</label>
      <input type="number" step="0.01" name="price" class="form-control mb-2" value="<?php echo $product['price']; ?>">
  
      <label>Description</label>
      <textarea name="description" class="form-control mb-2"><?php echo htmlspecialchars($product['description']); ?></textarea>
  
      <label>Stock</label>
      <input type="number" name="stock" class="form-control mb-2" value="<?php echo $product['stock']; ?>">
  
      <label>Category ID</label>
      <input type="number" name="category_id" class="form-control mb-2" value="<?php echo $product['category_id']; ?>">
  
      <label>Image Filename</label>
      <input type="text" name="image" class="form-control mb-2" value="<?php echo htmlspecialchars($product['image']); ?>">
  
      <button type="submit" class="btn btn-success">Save Changes</button>
      <a href="adminProductManagement.html" class="btn btn-secondary">Cancel</a>
    </form>
    <?php else: ?>
      <p>Product not found.</p>
    <?php endif; ?>
  </div>
  

  <hr>

  <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
    <input type="hidden" name="delete_product" value="1">
    <button type="submit" class="btn btn-danger">Delete This Product</button>
</form>

</div>

<script>
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('id');

  document.getElementById('editForm').onsubmit = function(e) {
    e.preventDefault();
    alert("Changes saved");
    window.location.href = 'adminProductManagement.php';
  }

  function confirmDelete() {
    if (confirm("Are you sure you want to delete this product?")) {
      alert("Product deleted ");
      window.location.href = 'adminProductManagement.php';
    }
  }
</script>
<script>
    function openNav() {
    document.getElementById("mySidenav").style.width = "100%";
    }
    function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    }
    </script>
</body>
</html>
