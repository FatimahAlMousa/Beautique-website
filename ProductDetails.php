<?php
session_start();
$conn = new mysqli("localhost", "root", "", "beautique_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<h2>Product not found</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title><?= htmlspecialchars($product['name']) ?> - Details</title>
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
            <a href="Homepage.php">Home</a>
            <a href="contactus.php">Contact Us</a>
            <a href="adminauthentication.php">Administrator's Authentication</a>
         </div>
         <span class="toggle_icon" onclick="openNav()"><img src="images/toggle-icon.png"></span>
         <a class="logo" href="Homepage.php"><img src="images/logo.png"></a>
         <form class="form-inline">
            <div class="login_text">
               <ul>
                  <li><a href="Checkout.php"><img src="images/cart-icon.png" width="30" height="30"><span class="cart-count"><?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span></a></li>
               </ul>
            </div>
         </form>
      </nav>
   </div>
</div>

<div class="product_detail_section layout_padding">
   <div class="container">
      <div class="row">
         <div class="col-md-6">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" class="img-2" alt="<?= htmlspecialchars($product['name']) ?>">
         </div>
         <div class="col-md-6">
            <h1 class="product_title"><?= htmlspecialchars($product['name']) ?> - $<?= number_format($product['price'], 2) ?></h1>
            <p class="product_description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <form id="cart-form" method="post" action="">
               <label for="quantity">Select Quantity:</label><br>
               <div style="display: flex; align-items: center; gap: 10px;">
                  <button type="button" class="btn btn-secondary" onclick="changeQty(-1)">-</button>
                  <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" readonly style="width: 60px; text-align: center;">
                  <button type="button" class="btn btn-secondary" onclick="changeQty(1)">+</button>
               </div>
               <br>
               <p><strong>Total Price: $<span id="total-price"><?= number_format($product['price'], 2) ?></span></strong></p>

               <input type="hidden" name="action_type" id="action_type" value="">
               <button type="submit" name="add_to_cart" class="btn btn-warning" onclick="setAction('add')" <?= $product['stock'] == 0 ? 'disabled' : '' ?>>Add to Cart</button>
<button type="submit" name="add_to_cart" class="btn btn-success" onclick="setAction('add_and_checkout')" <?= $product['stock'] == 0 ? 'disabled' : '' ?>>Add & Checkout</button>

            </form>

            <p>
               Need help with this product? 
               <a href="help_product.php?id=<?= $product['id'] ?>">Click here for product support</a><br>
               Or <a href="Contactus.php">contact us</a> for general inquiries.
            </p>

            <h3 class="price_text">Available Stock: <?= $product['stock'] ?></h3>
            <?php if ($product['stock'] == 0): ?>
   <p style="color: red; font-weight: bold;">❌ Out of stock – This product is currently unavailable.</p>
<?php endif; ?>

         </div>
      </div>
   </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = intval($_POST['quantity']);
    $action = $_POST['action_type'];
    $maxQty = 10;

    if ($qty < 1) {
        echo "<script>alert('Quantity must be at least 1.');</script>";
    } elseif ($qty > $maxQty) {
        echo "<script>alert('Maximum allowed quantity is 10.');</script>";
    } elseif ($qty > $product['stock']) {
        echo "<script>alert('Requested quantity exceeds available stock.');</script>";
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product['id']) {
                $totalQty = $item['quantity'] + $qty;
                if ($totalQty > $maxQty) {
                    echo "<script>alert('Total quantity in cart cannot exceed 10.');</script>";
                    exit;
                }
                $item['quantity'] = $totalQty;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $qty,
                'image' => $product['image']
            ];
        }

        if ($action === 'add_and_checkout') {
            echo "<script>alert('Product added. Redirecting to checkout.'); location.href='checkout.php';</script>";
        } else {
            echo "<script>alert('Product added to cart!'); location.href='ProductDetails.php?id=$id';</script>";
        }
    }
}
?>

<script>
function updateTotalPrice() {
    var price = <?= $product['price'] ?>;
    var qty = parseInt(document.getElementById("quantity").value);
    var total = price * qty;
    document.getElementById("total-price").innerText = total.toFixed(2);
}

function changeQty(change) {
    var qtyInput = document.getElementById("quantity");
    var current = parseInt(qtyInput.value);
    var max = 10;
    var min = 1;
    var stock = <?= $product['stock'] ?>;
    var newQty = current + change;

    if (newQty < min) {
        alert('❌ You cannot select less than 1 item.');
    } else if (newQty > max) {
        alert('❌ You cannot select more than 10 items.');
    } else if (newQty > stock) {
        alert('❌ Not enough stock available.');
    } else {
        qtyInput.value = newQty;
        updateTotalPrice();
    }
}

function setAction(action) {
    document.getElementById("action_type").value = action;
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
