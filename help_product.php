<?php
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
    echo "<h2>Product not found.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Help - <?= htmlspecialchars($product['name']) ?></title>
   <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body style="padding: 30px; font-family: Arial, sans-serif; background-color: #f8f9fa;">
   <div class="container">
      <h1 class="mb-4">Help for: <span class="text-primary"><?= htmlspecialchars($product['name']) ?></span></h1>

      <div class="card mb-4">
         <div class="card-header bg-info text-white">📦 Product Description</div>
         <div class="card-body">
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
         </div>
      </div>

      <div class="card mb-4">
         <div class="card-header bg-warning text-dark">❓ Frequently Asked Questions</div>
         <div class="card-body">
            <ul>
               <li><strong>Q:</strong> How do I use this product?<br>
                   <strong>A:</strong> Please follow the usage instructions below.
               </li>
               <li><strong>Q:</strong> Can I return this product?<br>
                   <strong>A:</strong> Returns are accepted within 7 days if the product is unopened.
               </li>
               <li><strong>Q:</strong> Is it available in other colors/sizes?<br>
                   <strong>A:</strong> Please visit the <a href="Homepage.php">products page</a> to view available variations.
               </li>
            </ul>
         </div>
      </div>

      <div class="card mb-4">
         <div class="card-header bg-success text-white">📝 Usage Instructions</div>
         <div class="card-body">
         <?php
         switch ($product['category_id']) {
            case 1:
               echo "<ol>
                  <li>Spray on pulse points (neck, wrists).</li>
                  <li>Avoid rubbing after application.</li>
                  <li>Keep away from eyes and broken skin.</li>
               </ol>";
               break;
            case 2:
               echo "<ol>
                  <li>Apply to clean, dry skin.</li>
                  <li>Use appropriate brushes or applicators.</li>
                  <li>Remove makeup before sleeping.</li>
               </ol>";
               break;
            case 3:
               echo "<ol>
                  <li>Use on clean face, morning and night.</li>
                  <li>Apply a small amount and massage gently.</li>
                  <li>Use sunscreen if product contains actives (like Vitamin C).</li>
               </ol>";
               break;
            case 4:
               echo "<ol>
                  <li>Apply to damp or dry hair as directed.</li>
                  <li>Avoid applying to scalp unless instructed.</li>
                  <li>Rinse thoroughly (if it's a rinse-out product).</li>
               </ol>";
               break;
            default:
               echo "<ol>
                  <li>Read the label or included manual before use.</li>
                  <li>Use as directed — avoid overuse or misuse.</li>
                  <li>Store in a cool, dry place away from sunlight.</li>
               </ol>";
               break;
         }
         ?>
         </div>
      </div>

      <div class="card mb-4">
         <div class="card-header bg-secondary text-white">📬 Still Need Help?</div>
         <div class="card-body">
            <p>If you still have questions, please <a href="Contactus.php">contact us</a> and mention the product: <strong><?= htmlspecialchars($product['name']) ?></strong></p>
         </div>
      </div>

      <a href="ProductDetails.php?id=<?= $product['id'] ?>" class="btn btn-primary">← Back to Product</a>
   </div>
</body>
</html>
