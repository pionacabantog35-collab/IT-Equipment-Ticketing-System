<?php
require_once "config.php";
include "session-checker.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU Home Page</title>
     <link rel="stylesheet" type="text/css" href="css1/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="css1/font-awesome.min.css" />
  <link rel="stylesheet" type="text/css" href="css1/script.js" />
    <link rel="stylesheet" href="css1/indexpage-style.css">

    
</head>
<body>

    <div class="header-container">
        <img src="aulogo.png" alt="Arellano University Logo" class="logo">
        <h1>Arellano University Home Page</h1>
        <a href="login.php" class="logout-container">
            <img src="logout.png" alt="Logout" class="logout-btn">
        </a>
    </div>
    <div class="main-content"> 
    <div class="slider">
        <div class="slides">
            <div class="slide">
                <img src="chiefs.jpg" alt="Arellano Chiefs" class="auimage">
            </div>
            <div class="slide">
                <img src="banner2.jpg" alt="Another Image" class="auimage">
            </div>
           
        </div>
        <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
        <button class="next" onclick="changeSlide(1)">&#10095;</button>
    </div><!-- Slider for the main content -->
    <div class="welcome-container">
     <img src="users.png" alt="User Image" class="user-image">
    <h2 class="welcome-message">
        Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
    </h2>
    <h3 class="account-type">
        Account type: <?php echo isset($_SESSION['usertype']) ? htmlspecialchars($_SESSION['usertype']) : 'Not Available'; ?>
    </h3>
</div>

  <!-- Feature Section -->

        <div class="container">
            <a href="admin-accountsmanagement.php" class="card">
                <img src="users.png" alt="Equipment">
                 <p class="card-title">Accounts</p>
                
            </a>
        <div class="container">
            <a href="admin-equipmentmanagement.php" class="card">
                <img src="equipments.png" alt="Equipment">
                 <p class="card-title">Equipments</p>
                
            </a>
            <a href="admin-ticketmanagement.php" class="card">
                <img src="tickets.png" alt="Tickets">
                <p class="card-title">Tickets</p>
                
            </a>
        </div>
    </div>
        <!-- About Section -->
        <section class="about_section layout_padding-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-6">
                        <div class="detail-box">
                            <h2>About us</h2>
                            <p>
                               Arellano University (AU) is a private, coeducational, nonsectarian university located in Manila, the Philippines. It was founded in 1938 as a law school by Florentino Cayco Sr., the first Filipino Undersecretary of Public Instruction.
                            </p>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-6">
                        <div class="img-box">
                            <img src="aboutus.jpeg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <section class="info_section">
    <div class="container">

        <div class="info_items">
            <div class="item">
                <div class="img-box">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                </div>
                <p>2600 Legarda St., Sampaloc, Manila</p>
            </div>

            <div class="item">
                <div class="img-box">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                </div>
                <p>&copy; 2025 Piona Cabantog</p>
            </div>

            <div class="item">
                <div class="img-box">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </div>
                <p>mail.arellano.edu.ph</p>
            </div>
        </div>

    </div>
</section>


    </div> <!-- End of main content wrapper -->
  <script src="css1/script.js"></script>
</body>
</html>
