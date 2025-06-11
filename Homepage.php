<?php                // Fatimah Al-Haidar 2220005159
session_start();
$conn = new mysqli("localhost", "root", "", "beautique_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM products";

$pastPurchases = [];
$recommendations = [];

if (isset($_COOKIE['past_purchases'])) {
    $pastPurchases = json_decode($_COOKIE['past_purchases'], true);
    if (!empty($pastPurchases)) {
        $placeholders = implode(',', array_fill(0, count($pastPurchases), '?'));
        $types = str_repeat('i', count($pastPurchases));

        $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$pastPurchases);
        $stmt->execute();
        $past_result = $stmt->get_result();
        $past_purchased_products = $past_result->fetch_all(MYSQLI_ASSOC);

        
        
$cat_stmt = $conn->prepare("SELECT DISTINCT category_id FROM products WHERE id IN ($placeholders)");
$cat_stmt->bind_param($types, ...$pastPurchases);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();

$category_ids = [];
while ($row = $cat_result->fetch_assoc()) {
    $category_ids[] = $row['category_id'];
}


if (!empty($category_ids)) {
    $cat_placeholders = implode(',', array_fill(0, count($category_ids), '?'));
    $cat_types = str_repeat('i', count($category_ids));
    $all_params = array_merge($category_ids, $pastPurchases);

    $filter_stmt = $conn->prepare("SELECT * FROM products WHERE category_id IN ($cat_placeholders) AND id NOT IN ($placeholders) LIMIT 3");
    $filter_stmt->bind_param(str_repeat('i', count($all_params)), ...$all_params);
    $filter_stmt->execute();
    $rec_result = $filter_stmt->get_result();
    $recommendations = $rec_result->fetch_all(MYSQLI_ASSOC);
}

    }
}


$result = $conn->query($sql);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && ($_POST['action_type'] === 'add_to_cart' || $_POST['action_type'] === 'add_to_cart_checkout')) {
    $product_id = $_POST['product_id'];
    $qty = intval($_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] += $qty;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'quantity' => $qty,
        ];
    }

    if ($_POST['action_type'] === 'add_to_cart_checkout') {
        echo json_encode(["message" => "Product added. Redirecting to cart...", "redirect" => "checkout.php"]);
    } else {
        echo json_encode(["message" => "Product added to cart!"]);
    }
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Beautique</title>
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
            <a href="Contactus.php">Contact Us</a>
            <a href="adminAuthentication.php">Administrator's Authentication</a>
         </div>
         <span class="toggle_icon" onclick="openNav()"><img src="images/toggle-icon.png"></span>
         <a class="logo" href="homepage.php"><img src="images/logo.png"></a>
         <form class="form-inline">
            <div class="login_text">
               <ul>
                  <li><a href="checkout.php"><img src="images/cart-icon.png" width="30" height="30" style="object-fit: contain;"><span id="cart-count" class="badge badge-danger"><?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span></a></li>
               </ul>
            </div>
         </form>
      </nav>
   </div>
</div>

<div class="banner_section layout_padding">
   <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
         <div class="carousel-item active">
            <div class="container">
               <div class="row">
                  <div class="col-sm-6">
                     <h1 class="banner_taital">Shop Our <br>Products</h1>
                     <p class="banner_text">Discover Beautique's premium beauty products.</p>
                  </div>
                  <div class="col-sm-6">
                     <img src="images/banner-img-1.png" class="banner-img-1" style="float: right;">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Past Purchases -->
<?php if (!empty($past_purchased_products)): ?>
<div class="product_section layout_padding" style="margin-bottom: 0;">
   <div class="container">
      <h2 class="product_taital">Your Past Purchases</h2>
      <div class="row text-center">
         <?php foreach ($past_purchased_products as $product): ?>
         <div class='col-lg-4 col-sm-6'>
   <div class='product_box'>
      <h4 class='bursh_text'><?= htmlspecialchars($product['name']) ?></h4>
      <p class='lorem_text'><?= htmlspecialchars($product['description']) ?></p>
      <img src='images/<?= htmlspecialchars($product['image']) ?>' class='img-1'>
      <div class='btn_main'>
         <div class='buy_bt'>
            <ul>
               <li class='active'><a href='Productdetails.php?id=<?= $product['id'] ?>'>More Details</a></li>
            </ul>
         </div>
         <h3 class='price_text'>Price $<?= number_format($product['price'], 2) ?></h3>
         <form method='post' class='add-to-cart-form'>
            <input type='hidden' name='product_id' value='<?= $product['id'] ?>'>
            <input type='hidden' name='quantity' value='1'>
            <input type='hidden' name='action_type' value=''>
            <button type='button' class='btn btn-warning' onclick='submitCart(this, "add_to_cart")'>Add to Cart</button>
            <button type='button' class='btn btn-success' onclick='submitCart(this, "add_to_cart_checkout")'>Add to Cart & Proceed to Checkout</button>
         </form>
      </div>
   </div>
</div>
         <?php endforeach; ?>
      </div>
   </div>
</div>
<?php endif; ?>

<!-- Products You Might Like -->
<?php if (!empty($recommendations)): ?>
<div class="product_section layout_padding" style="margin-top: -30px;">
   <div class="container">
      <h2 class="product_taital">Products You Might Like</h2>
	  <p class="product_text">Products you might like based on your past purchases.</p>
      <div class="row text-center">
         <?php foreach ($recommendations as $product): ?>
         <div class='col-lg-4 col-sm-6'>
   <div class='product_box'>
      <h4 class='bursh_text'><?= htmlspecialchars($product['name']) ?></h4>
      <p class='lorem_text'><?= htmlspecialchars($product['description']) ?></p>
      <img src='images/<?= htmlspecialchars($product['image']) ?>' class='img-1'>
      <div class='btn_main'>
         <div class='buy_bt'>
            <ul>
               <li class='active'><a href='Productdetails.php?id=<?= $product['id'] ?>'>More Details</a></li>
            </ul>
         </div>
         <h3 class='price_text'>Price $<?= number_format($product['price'], 2) ?></h3>
         <form method='post' class='add-to-cart-form'>
            <input type='hidden' name='product_id' value='<?= $product['id'] ?>'>
            <input type='hidden' name='quantity' value='1'>
            <input type='hidden' name='action_type' value=''>
            <button type='button' class='btn btn-warning' onclick='submitCart(this, "add_to_cart")'>Add to Cart</button>
            <button type='button' class='btn btn-success' onclick='submitCart(this, "add_to_cart_checkout")'>Add to Cart & Proceed to Checkout</button>
         </form>
      </div>
   </div>
</div>
         <?php endforeach; ?>
      </div>
   </div>
</div>
<?php endif; ?>

<!-- Product Section Start -->
<div class="product_section layout_padding">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <h1 class="product_taital">Our Products</h1>
            <p class="product_text">Explore our exclusive beauty products carefully selected for you.</p>
         </div>
      </div>
      <div class="product_section_2 layout_padding">
         <div class="row text-center">
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='col-lg-4 col-sm-6'>";
                    echo "<div class='product_box'>";
                    echo "<h4 class='bursh_text'>" . $row['name'] . "</h4>";
                    echo "<p class='lorem_text'>" . $row['description'] . "</p>";
                    echo "<img src='images/" . $row['image'] . "' class='img-1'>";
                    echo "<div class='btn_main'>";
                    echo "<div class='buy_bt'>";
                    echo "<ul>";
                    echo "<li class='active'><a href='Productdetails.php?id=" . $row['id'] . "'>More Details</a></li>";
                    echo "</ul>";
                    echo "</div>";
                    echo "<h3 class='price_text'>Price $" . $row['price'] . "</h3>";
                    
                    echo "<form method='post' class='add-to-cart-form'>";
					echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
					echo "<input type='hidden' name='quantity' value='1'>";
					echo "<input type='hidden' name='action_type' value=''>";
					echo "<button type='button' class='btn btn-warning' onclick='submitCart(this, \"add_to_cart\")'>Add to Cart</button>";
					echo "<button type='button' class='btn btn-success' onclick='submitCart(this, \"add_to_cart_checkout\")'>Add to Cart & Proceed to Checkout</button>";
					echo "</form>";

                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No products found</p>";
            }
            ?>
         </div>
      </div>
   </div>
</div>
<!-- Product Section End -->

<!-- About Us Section Start -->
<div class="about_section layout_padding">
   <div class="container">
      <div class="about_section_main">
         <div class="row">
            <div class="col-md-6">
               <div class="about_taital_main">
                  <h1 class="about_taital">About Our Beauty Store</h1>
                  <p class="about_text">We are dedicated to providing high-quality beauty products to enhance your confidence and style. 
                  You can contact us for more information.</p>
                  <div class="readmore_bt"><a href="contactus.php">Contact Us</a></div>
               </div>
            </div>
            <div class="col-md-6">
               <div><img src="images/image-2.jpg" class="image-2.jpg"></div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- About Us Section End -->

<script>
function submitCart(button, actionType) {
    const form = button.closest('form');
    form.querySelector('input[name="action_type"]').value = actionType;

    const formData = new FormData(form);

    fetch('Homepage.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Something went wrong. Please try again.");
    });
}

function openNav() {
    document.getElementById("mySidenav").style.width = "100%";
}
function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}
</script>


</body>
</html>

<?php
// Close the database 
$conn->close();
?>

