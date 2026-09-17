<?php
require_once 'config.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $ticketnumber = date('YmdHis');
    $problem = trim($_POST['problem_type']);
    $details = trim($_POST['problem_details']); 
    $createdby = $_SESSION['username'] ?? 'Unknown';
    $datecreated = date('Y-m-d H:i:s');
    $status = "PENDING"; 
    $assignedto = NULL;
    $dateassigned = NULL;
    $datecompleted = NULL;
    $approvedby = NULL;
    $dateapproved = NULL;

    // Prepare SQL statement
    $sql = "INSERT INTO tbltickets 
            (ticketnumber, problem, details, status, createdby, datecreated, assignedto, dateassigned, datecompleted, approvedby, dateapproved) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssssssss", 
            $ticketnumber, 
            $problem, 
            $details, 
            $status, 
            $createdby, 
            $datecreated, 
            $assignedto, 
            $dateassigned, 
            $datecompleted, 
            $approvedby, 
            $dateapproved
        );
        
         if (mysqli_stmt_execute($stmt)) {
            // Success message stored in session
            $_SESSION['success_message'] = "Ticket #$ticketnumber has been successfully created.";
            header("Location: user-ticketmanagement.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error: Could not create ticket.";
            header("Location: user-createticket.php");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error_message'] = "Error preparing SQL statement.";
        header("Location: user-createticket.php");
        exit();
    }
    
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ticket - AU Technical Support</title>
    <link rel="stylesheet" href="create-ticket.css">
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="user-indexpage.php">Home </a></li>
            <li><a href="user-equipmentmanagement.php">Equipment </a></li>
           <li><a href="user-ticketmanagement.php">Ticket </a></li>
           
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header-container">
            <h1>Ticket Management Page</h1>
        </div>
        <div class="container">
            <h2>Create New Ticket</h2>
            <form action="user-createticket.php" method="POST">
                <div class="form-group">
                    <label>Ticket Number:</label>
                    <input type="text" name="ticketnumber" value="<?php echo date('YmdHis'); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Problem Type:</label>
                    <select name="problem_type" required>
                        <option value="">Select Problem Type</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Connection">Connection</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Problem Details:</label>
                    <textarea name="problem_details" rows="4" required></textarea>
                </div>
                <div class="button-group">
                    <input type="submit" name="submit" value="Save" class="btn btn-primary">
                    <a href="user-ticketmanagement.php" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</body>
</html>
