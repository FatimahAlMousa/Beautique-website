<?php
session_start();
$conn = new mysqli("localhost", "root", "", "beautique_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['status' => 'error'];
    
    switch ($_POST['action']) {
        case 'update':
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);
            
            $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stock = $stmt->get_result()->fetch_assoc()['stock'];
            
            if ($quantity > 0 && $quantity <= $stock) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $product_id) {
                        $item['quantity'] = $quantity;
                        $response['status'] = 'success';
                        break;
                    }
                }
            }
            break;
            
        case 'delete':
            $product_id = intval($_POST['product_id']);
            $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($item) => $item['id'] != $product_id);
            $response['status'] = 'success';
            break;
            
        case 'empty':
            unset($_SESSION['cart']);
            $response['status'] = 'success';
            break;
            
        case 'purchase':
            if (!empty($_SESSION['cart'])) {
                $conn->autocommit(FALSE);
                try {
                    foreach ($_SESSION['cart'] as $item) {
                        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                        $stmt->bind_param("iii", $item['quantity'], $item['id'], $item['quantity']);
                        if (!$stmt->execute() || $stmt->affected_rows === 0) {
                            throw new Exception("Insufficient stock");
                        }
						
						
						$purchased_ids = array_column($_SESSION['cart'], 'id');

						
						$existing = isset($_COOKIE['past_purchases']) ? json_decode($_COOKIE['past_purchases'], true) : [];
						$updated = array_unique(array_merge($existing, $purchased_ids));

						
						setcookie('past_purchases', json_encode($updated), time() + (86400 * 30), "/");
                    }
                    $conn->commit();
                    unset($_SESSION['cart']);
                    $response = ['status' => 'success', 'redirect' => 'Homepage.php'];
                } catch (Exception $e) {
                    $conn->rollback();
                }
            }
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_column($_SESSION['cart'], 'id');
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id IN (?" . str_repeat(",?", count($ids)-1) . ")");
    $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    foreach ($_SESSION['cart'] as $item) {
        $product = current(array_filter($products, fn($p) => $p['id'] == $item['id']));
        if ($product) {
            $total = $product['price'] * $item['quantity'];
            $cart_items[] = $product + ['quantity' => $item['quantity'], 'total' => $total];
            $subtotal += $total;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Beautique - Checkout</title>
   <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" href="css/responsive.css">
   <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
   <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
   <link href="https://fonts.googleapis.com/css?family=Great+Vibes|Open+Sans:400,700&display=swap&subset=latin-ext" rel="stylesheet">
</head>
<body>


<div id="purchaseModal" class="purchase-modal">
   <div class="purchase-modal-content">
      <h3>Purchase Completed Successfully!</h3>
      <div class="purchase-items" id="purchaseItems">
         
      </div>
      <div class="purchase-total" id="purchaseTotal">
         
      </div>
      <button class="btn btn-success" onclick="continueShopping()">Continue Shopping</button>
   </div>
</div>


<div class="header_section">
   <div class="container-fluid">
      <nav class="navbar navbar-light bg-light justify-content-between">
         <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="Homepage.php">Home</a>
            <a href="Contactus.php">Contact Us</a>
            <a href="adminAuthentication.php">Administrator's Authentication</a>
         </div>
         <span class="toggle_icon" onclick="openNav()"><img src="images/toggle-icon.png"></span>
         <a class="logo" href="Homepage.php"><img src="images/logo.png"></a>
         <form class="form-inline">
            <div class="login_text">
               <ul>
                  <li><a href="Checkout.php"><img src="images/cart-icon.png" width="30" height="30"><span class="cart-count"><?= !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span></a></li>
               </ul>
            </div>
         </form>
      </nav>
   </div>
</div>


<div class="product_section layout_padding">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <h1 class="product_taital">Your Shopping Cart</h1>
            <p class="product_text">Review your selected items</p>
         </div>
      </div>
      
      <div class="row">
         <div class="col-md-12">
            <?php if (empty($cart_items)): ?>
               <div class="alert alert-info text-center">
                  Your cart is empty. <a href="Homepage.php">Continue shopping</a>
               </div>
            <?php else: ?>
               <div class="cart-items">
                  <?php foreach ($cart_items as $item): ?>
                     <div class="cart-item">
                        <img src="images/<?= htmlspecialchars($item['image']) ?>" class="img-1" style="width:100px;height:100px;object-fit:cover;">
                        <div class="cart-item-details">
                           <h4 class="bursh_text"><?= htmlspecialchars($item['name']) ?></h4>
                           <p class="lorem_text">Price: $<?= number_format($item['price'], 2) ?></p>
                           <div class="quantity-control" style="display:flex;align-items:center;gap:10px;">
                              <button class="btn btn-sm btn-secondary" onclick="updateCart(<?= $item['id'] ?>, -1)">-</button>
                              <input type="number" value="<?= $item['quantity'] ?>" min="1" style="width:60px;text-align:center;" onchange="updateCart(<?= $item['id'] ?>, 0, this.value)">
                              <button class="btn btn-sm btn-secondary" onclick="updateCart(<?= $item['id'] ?>, 1)">+</button>
                           </div>
                        </div>
                        <div class="cart-item-total">
                           <h3 class="price_text">$<?= number_format($item['total'], 2) ?></h3>
                           <button class="btn btn-sm btn-danger" onclick="removeItem(<?= $item['id'] ?>)">
                              <i class="fa fa-trash"></i> Remove
                           </button>
                        </div>
                     </div>
                  <?php endforeach; ?>
               </div>
               
               <div class="cart-summary mt-4">
                  <div class="row">
                     <div class="col-md-6">
                        <button class="btn btn-danger" onclick="emptyCart()">
                           <i class="fa fa-trash"></i> Empty Cart
                        </button>
                     </div>
                     <div class="col-md-6 text-right">
                        <div class="order-summary">
                           <div class="summary-row">
                              <span>Subtotal (<?= array_sum(array_column($cart_items, 'quantity')) ?> items):</span>
                              <span>$<?= number_format($subtotal, 2) ?></span>
                           </div>
                           <div class="summary-row total">
                              <span>Total:</span>
                              <span>$<?= number_format($subtotal, 2) ?></span>
                           </div>
                        </div>
                        <button class="btn btn-success mt-3" onclick="checkout()">
                           <i class="fa fa-check"></i> Complete Purchase
                        </button>
                     </div>
                  </div>
               </div>
            <?php endif; ?>
         </div>
      </div>
   </div>
</div>

<script>

function updateCart(productId, change, newQuantity = null) {
    const quantity = newQuantity !== null ? parseInt(newQuantity) : 
        parseInt(document.querySelector(`input[onchange*="${productId}"]`).value) + change;
        if (quantity < 1) {
        alert('❌ You cannot select less than 1 item.');
        return;
    } else if (quantity > 10) {
        alert('❌ You cannot select more than 10 items.');
        return;
    }
    
    fetch('Checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    }).then(res => res.json()).then(data => {
        if (data.status === 'success') location.reload();
    });
}

function removeItem(productId) {
    fetch('Checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&product_id=${productId}`
    }).then(res => res.json()).then(data => {
        if (data.status === 'success') location.reload();
    });
}

function emptyCart() {
    const confirmEmpty = confirm("🛒 Are you sure you want to empty your cart?");
    if (!confirmEmpty) return;

    fetch('Checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=empty'
    }).then(res => res.json()).then(data => {
        if (data.status === 'success') location.reload();
    });
}


function checkout() {
    fetch('Checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=purchase'
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showPurchaseConfirmation(data.items, data.total); 
        } else {
            alert(data.message);
        }
    });
}


function showPurchaseConfirmation() {

    const cartItems = <?= json_encode($cart_items) ?>;
    const subtotal = <?= $subtotal ?>;
    const modal = document.getElementById('purchaseModal');
    const itemsContainer = document.getElementById('purchaseItems');
    const totalContainer = document.getElementById('purchaseTotal');
    

    itemsContainer.innerHTML = '';
    

    cartItems.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'purchase-item';
        itemElement.innerHTML = `
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>${item.name} (${item.quantity}x)</span>
                <span>$${item.total.toFixed(2)}</span>
            </div>
        `;
        itemsContainer.appendChild(itemElement);
    });
    

    totalContainer.textContent = `Total: $${subtotal.toFixed(2)}`;
    

    modal.style.display = 'flex';
}


function continueShopping() {
    document.getElementById('purchaseModal').style.display = 'none';
    window.location.href = 'Homepage.php';
}


function openNav() {
    document.getElementById("mySidenav").style.width = "250px"; 
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}


</script>

</body>
</html>