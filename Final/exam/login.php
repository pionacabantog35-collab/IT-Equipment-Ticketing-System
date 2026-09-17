<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - AU Technical Support Management System</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>
<body>
    
    <div class="container" id="container">
        <div class="form-container sign-in-container">
            <h2>Login</h2>
            <!-- Error message will appear here (below the h2) -->
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                require_once 'config.php'; 
                session_start(); // Start the session

                $username = trim($_POST['txtusername']);
                $password = trim($_POST['txtpassword']);

                if (empty($username) || empty($password)) {
                    echo "<div class='error-message'>Please enter both username and password.</div>";
                } else {
                    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND status = 'ACTIVE'";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $username);
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);
                            if ($account = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                // Plain text password check (NOT RECOMMENDED for production)
                                if ($password === $account['password']) { 
                                    $_SESSION['username'] = $account['username'];
                                    $_SESSION['usertype'] = $account['usertype'];

                                    if ($_SESSION['usertype'] === "ADMINISTRATOR") {
                                        header("Location: admin-indexpage.php");
                                    } elseif ($_SESSION['usertype'] === "TECHNICAL") {
                                        header("Location: tech-indexpage.php");
                                    } else {
                                        header("Location: user-indexpage.php");
                                    }
                                    exit();
                                } else {
                                    echo "<div class='error-message'>Incorrect password.</div>";
                                }
                            } else {
                                echo "<div class='error-message'>User not found or inactive.</div>";
                            }
                        } else {
                            echo "<div class='error-message'>Error executing query.</div>";
                        }
                    } else {
                        echo "<div class='error-message'>Error preparing statement.</div>";
                    }
                }
                mysqli_close($link);
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="input-group">
                    <input type="text" id="username" name="txtusername" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="txtpassword" placeholder="Password" required>
                </div>
                <button type="submit" name="btnlogin" class="login-btn">Login</button>
            </form>
        </div>
        <div class="overlay-panel overlay-right">
            <img src="aulogo.png" alt="Overlay Image" style="max-width: 50%; height: auto;">
            <h1>ARELLANO UNIVERSITY</h1>
            <p>Technical Management System</p>
        </div>
    </div>
</body>
</html>
