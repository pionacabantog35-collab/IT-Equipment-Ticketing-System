<?php
require_once "config.php";
include "session-checker.php";

if (isset ($_POST['btnsubmit']))
{	
	$sql = "UPDATE	tblaccounts SET password = ?, usertype = ?, status = ? WHERE username = ?";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "ssss", $_POST['txtpassword'], $_POST['cmbtype'], $_POST['rbstatus'], $_GET['username']);
		if(mysqli_stmt_execute($stmt))
{
	$sql  = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
	if($stmt = mysqli_prepare($link, $sql))
	{
		$date = date("d/m/Y");
		$time = date("h:i:sa");
		$action = "Update";
		$module = "Accounts Management";
		mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_GET['username'], $_SESSION['username']);
		if(mysqli_stmt_execute($stmt))
		{
			$_SESSION['success_message'] = "User account '".$_GET['username']."' successfully updated.";
			header("location: admin-accountsmanagement.php");
			exit();
		}
	}
	else
	{
		echo "<font color = 'red'>Error on inserting logo.</font>";
	}
}

	}
	else
	{
		echo "<font color = 'red'>Error on updating account.</font>";
	}
}
else // laoding of current data
{
	if(isset($_GET['username']) && !empty(trim($_GET['username'])))
	{
		$sql = "SELECT * FROM tblaccounts WHERE username = ?";
		if($stmt = mysqli_prepare($link, $sql))
		{
			mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
			if(mysqli_stmt_execute($stmt))
			{
				$result = mysqli_stmt_get_result($stmt);
				$account = mysqli_fetch_array($result, MYSQLI_ASSOC);
			}
		}
		else
		{
			echo "<font color = 'red'>Error on loading the account data.</font>";
		}
	}
}

?>
<html>
	<title>Update Account Page - AU Technical Support Management Sysetme</title>
	<link rel="stylesheet" href="update-ticketstyle.css">
	<body>
		<div class="sidebar">
        <ul>
            <li><a href="admin-indexpage.php">Home </a></li>
            <li><a href="admin-accountsmanagement.php">Manage </a></li>
            <li><a href="admin-equipmentmanagement.php">Equipment </a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
    	  <div class="container">
        <h2>Update Account</h2>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST">
        	<div class="form-group">
    	<label for="username">Username:</label>
    	<input type="text" id="username" name="username" value="<?php echo $account['username']; ?>" readonly>
		</div>
         <div class="form-group">
         	 <label for="txtpassword">Password:</label>
           <input type="password" id="txtpassword" name="txtpassword" value="<?php echo $account['password']; ?>" placeholder="Password" required>
<br>
        </div>
            <div class="form-group">
            <label>Select usertype:</label><br>
            <select name="cmbtype" required>
                <option value=""> <?php echo $account['usertype']; ?></option>
                <option value="ADMINISTRATOR" <?php if ($account['usertype'] == 'ADMINISTRATOR') echo 'selected'; ?>>Administrator</option>
                <option value="TECHNICAL" <?php if ($account['usertype'] == 'TECHNICAL') echo 'selected'; ?>>Technical</option>
                <option value="STAFF" <?php if ($account['usertype'] == 'STAFF') echo 'selected'; ?>>Staff</option>
            </select><br>
        </div>
            <div class="radio-group">
                <label>Status:</label><br>
                <input type="radio" name="rbstatus" value="ACTIVE" <?php if ($account['status'] == 'ACTIVE') echo 'checked'; ?>> Active<br>
            </label>
            <label>
                <input type="radio" name="rbstatus" value="INACTIVE" <?php if ($account['status'] == 'INACTIVE') echo 'checked'; ?>> Inactive<br>
            </label>
            </div>
            <div class="button-container">
    <button type="submit" name="btnsubmit" class="btn btn-update">Save</button>
    <a href="admin-accountsmanagement.php" class="btn btn-cancel">Cancel</a>
</div>
        </form>
    </div>
      </div>
    </div>
</body>
</html>