<?php
require_once 'config.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Retrieve form data
    $assetnumber = trim($_POST['assetnumber']);
    $serialnumber = trim($_POST['serialnumber']);
    $type = trim($_POST['type']);
    $manufacturer = trim($_POST['manufacturer']);
    $yearmodel = trim($_POST['yearmodel']);
    $description = trim($_POST['description']);
    $branch = trim($_POST['branch']);
    $department = trim($_POST['department']);
    $status = "Working"; 
    $createdby = $_SESSION['username'] ?? 'Unknown'; 
    $datecreated = date('Y-m-d H:i:s');

    // Validation
    if (!preg_match('/^\d{4}$/', $yearmodel)) {
        echo "<script>alert('Error: Year model must be a 4-digit number.'); window.history.back();</script>";
        exit();
    }

    // Check if asset number or serial number already exists
    $check_sql = "SELECT * FROM tblequipments WHERE assetnumber = ? OR serialnumber = ?";
    if ($check_stmt = mysqli_prepare($link, $check_sql)) {
        mysqli_stmt_bind_param($check_stmt, "ss", $assetnumber, $serialnumber);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            echo "<script>alert('Error: Asset number or Serial number already exists!'); window.history.back();</script>";
            exit();
        }
        mysqli_stmt_close($check_stmt);
    }

    // Insert equipment details
    $sql = "INSERT INTO tblequipments (assetnumber, serialnumber, type, manufacturer, yearmodel, description, branch, department, status, createdby, datecreated) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssssssss", $assetnumber, $serialnumber, $type, $manufacturer, $yearmodel, $description, $branch, $department, $status, $createdby, $datecreated);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Equipment added successfully!';
            header("Location: tech-equipmentmanagement.php");
            exit();
        } else {
            echo "<script>alert('Error: Could not add equipment.'); window.history.back();</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing SQL statement.');</script>";
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Equipment - AU Technical Support</title>
    <link rel="stylesheet" href="create-ticket.css">
    <script>
        function validateForm() {
            let year = document.forms["equipmentForm"]["yearmodel"].value;
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
             <li><a href="tech-indexpage.php">Home </a></li>
            <li><a href="tech-equipmentmanagement.php">Equipment </a></li>
            <li><a href="tech-ticketmanagement.php">Ticket </a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header-container">
            <h1>Equipment Management Page</h1>
        </div>
        <div class="container">
            <h2>Add Equipment</h2>
            <form name="equipmentForm" action="tech-addequipment.php" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label>Asset Number:</label>
                    <input type="text" name="assetnumber" required>
                </div>

                <div class="form-group">
                    <label>Serial Number:</label>
                    <input type="text" name="serialnumber" required>
                </div>


                <div class="form-group">
                    <label>Equipment Type:</label>
                    <select name="type" required>
                        <option value="">Select Equipment Type</option>
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
                    <input type="text" name="manufacturer" required>
                </div>

                <div class="form-group">
                    <label>Year Model:</label>
                    <input type="text" name="yearmodel" required>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label>Branch:</label>
                    <select name="branch" required>
                        <option value="">Select Branch</option>
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
                    <select name="department" required>
                        <option value="">Select Department</option>
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

                <div class="form-group button-group">
                    <input type="submit" name="submit" value="Save" class="btn btn-primary">
                    <a href="tech-equipmentmanagement.php" class="cancel-btn btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
