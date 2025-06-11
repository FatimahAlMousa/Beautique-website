<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    include('mail.php');
    // sendEmail($_POST['email'], "Beautique Contact Form Submission", "Full Name: " . $_POST['fullName'] . "\nEmail: " . $_POST['email'] . "\nMobile: " . $_POST['mobile'], "\nMessage: " . $_POST['msg']);
    sendEmail($_POST['email'], "message from Beautique", "Full Name: " . $_POST['fullName'] . "<br/>Email: " . $_POST['email'] . "<br/>Mobile: " . $_POST['mobile'] . "<br/>Message: " . $_POST['msg']);
    $_SESSION['form_submitted'] = true;
    header('Location: ' . $_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" href="css/Contactus.css">
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes|Open+Sans:400,700&display=swap&subset=latin-ext" rel="stylesheet">

    <style>
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .popup-content h2 {
            color: #5a2d52;
            margin-bottom: 15px;
        }

        .popup-content p {
            margin-bottom: 20px;
            font-size: 16px;
        }

        .popup-close {
            background: #5a2d52;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
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
                            <li><a href="Checkout.php"><img src="images/cart-icon.png" width="30" height="30"><span class="cart-count"><?= !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span></a></li>
                        </ul>
                    </div>
                </form>
            </nav>
        </div>
    </div>

    <div class="contact-container">
        <div class="ffbox">
            <div class="ffbox1">
                <h1 class="gfg">Contact Us!</h1>
                <form id="contactForm" method="post">
                    <label for="fullName">
                        <i class="fa fa-solid fa-user" style="margin: 2px;"></i> Full Name:
                    </label>
                    <input type="text" id="fullName" name="fullName" required>

                    <label for="email">
                        <i class="fa fa-solid fa-envelope" style="margin: 2px;"></i>
                        Email Address:
                    </label>
                    <input type="email" id="email" name="email" required>

                    <label for="mobile">
                        <i class="fa fa-solid fa-phone" style="margin: 2px;"></i>
                        Contact No:
                    </label>
                    <input type="tel" id="mobile" name="mobile" required>

                    <label for="msg">
                        <i class="fa fa-solid fa-comment" style="margin: 2px;"></i>
                        Write Message:
                    </label>
                    <textarea id="msg" name="msg" rows="5" required></textarea>

                    <button type="submit" name="submit">Submit</button>
                </form>
            </div>
            <div class="map-div">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3567.095390750334!2d50.1904124!3d26.3928001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49ef85c961edaf%3A0x7b2db98f2941c78c!2sImam%20Abdulrahman%20Bin%20Faisal%20University!5e0!3m2!1sen!2ssa!4v1712937421234!5m2!1sen!2ssa"
                    width="370"
                    height="95%"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

    <div id="thankYouPopup" class="popup-overlay">
        <div class="popup-content">
            <h2>Thank You!</h2>
            <p>Thank you for contacting us. We'll get back to you soon!</p>
            <button class="popup-close" onclick="closePopup()">OK</button>
        </div>
    </div>

    <script>
        <?php if (isset($_SESSION['form_submitted'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('thankYouPopup').style.display = 'flex';
                <?php unset($_SESSION['form_submitted']); ?>
            });
        <?php endif; ?>

        function closePopup() {
            document.getElementById('thankYouPopup').style.display = 'none';
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