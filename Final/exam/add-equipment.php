<?php
require_once 'config.php'; 
session_start();

$errors = [];

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

    // Validation checks
    if (empty($assetnumber)) $errors[] = "Asset number is required.";
    if (empty($serialnumber)) $errors[] = "Serial number is required.";
    if (empty($type)) $errors[] = "Equipment type is required.";
    if (empty($manufacturer)) $errors[] = "Manufacturer is required.";
    if (empty($yearmodel)) {
        $errors[] = "Year model is required.";
    } elseif (!preg_match('/^\d{4}$/', $yearmodel)) {
        $errors[] = "Year model must be a 4-digit numeric value.";
    }
    if (empty($branch)) $errors[] = "Branch is required.";
    if (empty($department)) $errors[] = "Department is required.";

    // Uniqueness check
    $check_sql = "SELECT * FROM tblequipments WHERE assetnumber = ? OR serialnumber = ?";
    if ($check_stmt = mysqli_prepare($link, $check_sql)) {
        mysqli_stmt_bind_param($check_stmt, "ss", $assetnumber, $serialnumber);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $errors[] = "Asset number or Serial number already exists.";
        }
        mysqli_stmt_close($check_stmt);
    }

    // Insert into DB if no errors
    if (empty($errors)) {
        $sql = "INSERT INTO tblequipments (assetnumber, serialnumber, type, manufacturer, yearmodel, description, branch, department, status, createdby, datecreated) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssssssss", $assetnumber, $serialnumber, $type, $manufacturer, $yearmodel, $description, $branch, $department, $status, $createdby, $datecreated);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = 'Equipment added successfully!';
                header("Location: admin-equipmentmanagement.php");
                exit();
            } else {
                $errors[] = "Error: Could not add equipment.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Error preparing SQL statement.";
        }
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
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="admin-indexpage.php">Home</a></li>
            <li><a href="admin-equipmentmanagement.php">Equipment</a></li>
            <li><a href="admin-ticketmanagement.php">Ticket</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header-container">
            <h1>Equipment Management Page</h1>
        </div>
        <div class="container">
            <h2>Add Equipment</h2>

            <?php if (!empty($errors)): ?>
                <div id="error-messages" style="background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:10px; margin:20px 0; border-radius:4px;">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form name="equipmentForm" action="add-equipment.php" method="POST" onsubmit="return validateForm()">
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
                    <a href="admin-equipmentmanagement.php" class="cancel-btn btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function validateForm() {
        const form = document.forms["equipmentForm"];
        let errors = [];

        const assetnumber = form["assetnumber"].value.trim();
        const serialnumber = form["serialnumber"].value.trim();
        const type = form["type"].value.trim();
        const manufacturer = form["manufacturer"].value.trim();
        const yearmodel = form["yearmodel"].value.trim();
        const branch = form["branch"].value.trim();
        const department = form["department"].value.trim();

        // Validation rules
        if (!assetnumber) errors.push("Asset number is required.");
        if (!serialnumber) errors.push("Serial number is required.");
        if (!type) errors.push("Equipment type is required.");
        if (!manufacturer) errors.push("Manufacturer is required.");
        if (!yearmodel) {
            errors.push("Year model is required.");
        } else if (!/^\d{4}$/.test(yearmodel)) {
            errors.push("Year model must be a 4-digit number.");
        }
        if (!branch) errors.push("Branch is required.");
        if (!department) errors.push("Department is required.");

        // Display errors
        const errorContainer = document.getElementById("error-messages");
        if (errors.length > 0) {
            errorContainer.innerHTML = "<ul>" + errors.map(err => `<li>${err}</li>`).join("") + "</ul>";
            errorContainer.style.display = "block";
            return false;
        } else {
            errorContainer.style.display = "none";
        }

        return true;
    }
    </script>
</body>
</html>
