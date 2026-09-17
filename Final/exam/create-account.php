<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Account Page - AU Technical Support Management System</title>
    <link rel="stylesheet" href="create-ticket.css"> 
<body>
     <div class="sidebar">
        <ul>
            <li><a href="admin-indexpage.php">Home</a></li>
            <li><a href="admin-equipmentmanagement.php">Equipment </a></li>
            <li><a href="admin-accountsmanagement.php"> Accounts</a></li>
            <li><a href="admin-ticketmanagement.php">Tickets</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header-container">
            <h1>Accounts Management Page</h1>
        </div>
    <div class="container">
        <h2>Create New Account</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
           <div class="form-group">
        <label for="txtusername">Username:</label>
        <input type="text" id="txtusername" name="txtusername" required>
    </div>

    <div class="form-group">
        <label for="txtpassword">Password:</label>
        <input type="password" id="txtpassword" name="txtpassword" required>
    </div>

    <div class="form-group">
        <label for="cmbtype">Account:</label>
        <select id="cmbtype" name="cmbtype" required>
            <option value="">Select Account Type</option>
            <option value="ADMINISTRATOR">Administrator</option>
            <option value="TECHNICAL">Technical</option>
            <option value="STAFF">Staff</option>
        </select>
    </div>
    <div class="button-group">
                    <input type="submit" name="submit" value="Save" class="btn btn-primary">
                    <a href="admin-accountsmanagement.php" class="btn btn-secondary">Cancel</a>
                </div>
        </form>
    </div>
</body>
</html>
<?php
require_once "config.php";
include("session-checker.php");
if(isset($_POST['submit']))
{
    //
    $sql = "SELECT * FROM tblaccounts WHERE username =?";
    if($stmt = mysqli_prepare($link, $sql))
    {
        mysqli_stmt_bind_param($stmt, "s", $_POST['txtusername']);
        if(mysqli_stmt_execute($stmt))
        {
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 0)
            {
                //add account
                $sql = "INSERT INTO tblaccounts (username, password, usertype, status, createdby, datecreated) VALUES (?, ?, ?, ?, ?, ?)";
                if($stmt = mysqli_prepare($link, $sql))
                {
                    $status = 'ACTIVE';
                    $date = date("Y-m-d H:i:s");
                    mysqli_stmt_bind_param($stmt, "ssssss", $_POST['txtusername'], $_POST['txtpassword'], $_POST['cmbtype'], $status, $_SESSION['username'], $date);
                    if(mysqli_stmt_execute($stmt))
                    {
                        $_SESSION['success_message'] = "User account '{$_POST['txtusername']}' has been successfully created.";
                        header("Location: admin-accountsmanagement.php");
                        exit();
                    }
                }
                else
                {
                     echo"<div class='error-message'>ERROR on adding new account.</div>";
                }
            }
            else
            {
                echo"<div class='error-message'>Username is already in use.</div>";
            }
        }
    }
    else
    {
    echo "<div class='error-message'>ERROR on validating if username exists.</div>";
    }
}
?>

