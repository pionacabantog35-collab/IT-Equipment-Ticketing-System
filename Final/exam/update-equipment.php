<?php
require_once "config.php";
include "session-checker.php";

if (isset($_POST['btnsubmit'])) {    
    // Validation
    if (!preg_match('/^\d{4}$/', $_POST['txtyearmodel'])) {
        echo "<script>alert('Error: Year model must be a 4-digit number.'); window.history.back();</script>";
        exit();
    }

    $sql = "UPDATE tblequipments 
            SET serialnumber = ?, type = ?, manufacturer = ?, yearmodel = ?, description = ?, branch = ?, department = ?, status = ? 
            WHERE assetnumber = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssssss", 
            $_POST['txtserialnumber'], 
            $_POST['cmbtype'],
            $_POST['txtmanufacturer'], 
            $_POST['txtyearmodel'], 
            $_POST['txtdescription'], 
            $_POST['cmbbranch'], 
            $_POST['cmbdept'], 
            $_POST['rbstatus'], 
            $_GET['assetnumber']
        );
        if (mysqli_stmt_execute($stmt)) {
            // Logging the update
            $sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Update";
                $module = "Equipment Management";
                mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_GET['assetnumber'], $_SESSION['username']);
                mysqli_stmt_execute($stmt);
            }


            // Set a session message for the popup
            $_SESSION['success_message'] = "Equipment updated successfully!";
            header("location: admin-equipmentmanagement.php");
            exit();
        } else {
            $_SESSION['message'] = "Error updating equipment.";
        }
    }
} else {
    if (isset($_GET['assetnumber']) && !empty(trim($_GET['assetnumber']))) {
        $sql = "SELECT * FROM tblequipments WHERE assetnumber = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_GET['assetnumber']);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $equipment = mysqli_fetch_array($result, MYSQLI_ASSOC);
            }
        } else {
            echo "<font color='red'>Error fetching equipment details.</font>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Equipment - AU Technical Support</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function validateForm() {
            let year = document.forms["updateForm"]["txtyearmodel"].value;
            if (!/^[0-9]{4}$/.test(year)) {
                alert("Year model must be a 4-digit number.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

    <div class="sidebar">
        <ul>
             <li><a href="admin-indexpage.php">Home </a></li>
            <li><a href="admin-equipmentmanagement.php">Equipments </a></li>
            <li><a href="admin-accountsmanagement.php"> Accounts</a></li>
            <li><a href="admin-ticketsmanagement.php"> Ticket</a></li>

            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header-container">
            <h1>Equipment Management Page</h1>
        </div>
        <div class="container">
            <h1>Update Equipment</h1>
            <form name="updateForm" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label>Asset Number:</label>
                    <input type="text" name="assetnumber" value="<?php echo $equipment['assetnumber']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Serial Number:</label>
                    <input type="text" name="txtserialnumber" value="<?php echo $equipment['serialnumber']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Equipment Type:</label>
                    <select name="cmbtype" required>
                        <option value="<?php echo $equipment['type']; ?>" selected><?php echo $equipment['type']; ?></option>
                        <option value="Monitor">Monitor</option>
                        <option value="CPU">CPU</option>
                        <option value="Keyboard">Keyboard</option>
                        <option value="Mouse">Mouse</option>
                        <option value="AVR">AVR</option>
                        <option value="MAC">MAC</option>
                        <option value="Printer">Printer</option>
                        <option value="Projector">Projector</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Manufacturer:</label>
                    <input type="text" name="txtmanufacturer" value="<?php echo $equipment['manufacturer']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Year Model:</label>
                    <input type="text" name="txtyearmodel" value="<?php echo $equipment['yearmodel']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" name="txtdescription" value="<?php echo $equipment['description']; ?>" required>
                    </textarea>
                </div>
                <div class="form-group">
                    <label>Branch:</label>
                    <select name="cmbbranch" required>
                        <option value="<?php echo $equipment['branch']; ?>" selected><?php echo $equipment['branch']; ?></option>
                        <option value="Juan Sumulong Campus">Juan Sumulong Campus</option>
                        <option value="Andres Bonifacio Campus">Andres Bonifacio Campus</option>
                        <option value="Apolinario Mabini Campus">Apolinario Mabini Campus</option>
                        <option value="Elisa Esguerra Campus">Elisa Esguerra Campus</option>
                        <option value="Jose Abad Santos Campus">Jose Abad Santos Campus</option>
                        <option value="Jose Rizal Campus">Jose Rizal Campus</option>
                        <option value="Plaridel Campus">Plaridel Campus</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Department:</label>
                    <select name="cmbdept" required>
                        <option value="<?php echo $equipment['department']; ?>" selected><?php echo $equipment['department']; ?></option>
                        <option value="Business">Business</option>
                        <option value="Computer Studies">Computer Studies</option>
                        <option value="Criminology">Criminology</option>
                        <option value="Education">Education</option>
                        <option value="Human Resources">Human Resources</option>
                        <option value="Nursing">Nursing</option>
                        <option value="Political Science">Political Science</option>
                        <option value="Psychology">Psychology</option>
                        <option value="Accounting">Accounting</option>
                        <option value="Administration">Administration</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status:</label><br>
                    <input type="radio" name="rbstatus" value="Working" <?php if ($equipment['status'] == 'Working') echo 'checked'; ?>> Working
                    <input type="radio" name="rbstatus" value="On-repair" <?php if ($equipment['status'] == 'On-repair') echo 'checked'; ?>> On-Repair
                    <input type="radio" name="rbstatus" value="Retired" <?php if ($equipment['status'] == 'Retired') echo 'checked'; ?>> Retired
                </div>
                <div class="form-group button-group">
                    <input type="submit" name="btnsubmit" value="Save" class="btn btn-primary">
                    <a href="admin-equipmentmanagement.php" class="cancel-btn btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>