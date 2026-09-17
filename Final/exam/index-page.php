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
    <link rel="stylesheet" href="indexpage-style.css">
</head>
<body>

    <div class="header-container">
        <img src="aulogo.png" alt="Arellano University Logo" class="logo">
        <h1>Arellano University Home Page</h1>
        <a href="login.php" class="logout-container">
            <img src="logout.png" alt="Logout" class="logout-btn">
        </a>
    </div>

    <div class="main-content"> <!-- Wrapper for the main content -->
        <!-- Feature Section -->
        <section class="feature_section">
            <div class="container">
                <div class="feature_container">
                    <div class="box">
                        <div class="img-box">
                            <img src="users.png" alt="account">
                        </div>
                        <h5 class="name">ACCOUNTS</h5>
                    </div>
                    <div class="box active">
                        <div class="img-box">
                            <img src="equipments.png" alt="equipment">
                        </div>
                        <h5 class="name">EQUIPMENTS</h5>
                    </div>
                    <div class="box">
                        <div class="img-box">
                            <img src="tickets.png" alt="ticket">
                        </div>
                        <h5 class="name">TICKETS</h5>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about_section layout_padding-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-6">
                        <div class="detail-box">
                            <h2>About us</h2>
                            <p>
                                Arellano University (AU) is a private, coeducational, nonsectarian university located in Manila, the Philippines. It was founded in 1938 as a law school by Florentino Cayco Sr., the first Filipino Undersecretary of Public Instruction.[
                            </p>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-6">
                        <div class="img-box">
                            <img src="images/about-img.jpg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Info Section -->
        <section class="info_section">
            <div class="container">
                <h4>Get In Touch</h4>
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <div class="info_items">
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="#">
                                        <div class="item">
                                            <div class="img-box">
                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                            </div>
                                            <p>2600 Legarda St., Sampaloc, Manila</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="#">
                                        <div class="item">
                                            <div class="img-box">
                                                <i class="fa fa-phone" aria-hidden="true"></i>
                                            </div>
                                            <p>(02) 8734 7371</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="#">
                                        <div class="item">
                                            <div class="img-box">
                                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                            </div>
                                            <p>mail.arellano.edu.ph</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div> <!-- End of main content wrapper -->

</body>
</html>
