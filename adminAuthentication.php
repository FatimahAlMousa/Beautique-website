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

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminID = $conn->real_escape_string($_POST['adminID']);
    $adminPassword = $conn->real_escape_string($_POST['adminPassword']);

    $sql = "SELECT * FROM users WHERE user_id = '$adminID' AND password = '$adminPassword'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $adminID;
        header("Location: admin_product_management.php");
        exit();
    } else {
        $error = "Invalid ID or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Beauty Shop Admin Login</title>
  <link rel="stylesheet" href="css/adminStyle.css">
</head>
<body>

  <div class="login-container">
    <h2>Welcome, Beauty Admin </h2>
    <p>You’re the sparkle behind every glow ✨</p>

    <form method="post" action="adminProductManagement.php">
      <input type="text" name="adminID" placeholder="Admin ID" required>
      <input type="password" name="adminPassword" placeholder="Password" required>
      <button type="submit">Sign In</button>
      <a href="#" class="forgot-password" onclick="resetPassword()">Forgot password?</a>
    </form>

    <?php if ($error): ?>
      <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="footer-note">
      &copy; 2025 Beauty Shop. All rights reserved.
    </div>
  </div>

  <script>
    function resetPassword() {
      const email = prompt("Enter your email to receive password reset instructions:");
      if (email) {
        alert("Reset link sent to " + email + ". Please check your inbox.");
      }
    }
  </script>

</body>
</html>
