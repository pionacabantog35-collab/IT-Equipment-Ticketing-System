<?php
session_start();
require_once "config.php";

// Check if the user is an administrator
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'ADMINISTRATOR') {
    header("Location: login.php");
    exit();
}

// Ensure ticket number is provided
if (!isset($_GET['ticketnumber'])) {
    header("Location: ticket-management.php");
    exit();
}

$ticketnumber = $_GET['ticketnumber'];
$sql = "SELECT * FROM tbltickets WHERE ticketnumber = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $ticketnumber);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $ticket = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$ticket || ($ticket['status'] !== 'PENDING' && $ticket['status'] !== 'ON-GOING')) {
    die("Error: You can only assign tickets with PENDING or ON-GOING status.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $technician = $_POST['technician'];
    $dateAssigned = date("Y-m-d H:i:s");
    $status = 'ON-GOING';

    // Update ticket status and assignment
    $assign_sql = "UPDATE tbltickets SET assignedto = ?, status = ?, dateAssigned = ? WHERE ticketnumber = ?";
    if ($stmt = mysqli_prepare($link, $assign_sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $technician, $status, $dateAssigned, $ticketnumber);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $date = date("d/m/Y");
        $time = date("h:i:sa");
        $action = "Assign ";
        $module = "Ticket Management";
        $performedto = $ticketnumber;
        $performedby = $_SESSION['username'];

        $log_sql  = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $log_sql)) {
            mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $performedto, $performedby);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Redirect to ticket management after saving
    header("Location: admin-ticketmanagement.php");
    exit(); // Ensure the script stops here
}

$technicians_sql = "SELECT username FROM tblaccounts WHERE usertype = 'TECHNICAL'";
$technical = mysqli_query($link, $technicians_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Ticket</title>
    <link rel="stylesheet" href="assignstyle.css">
</head>
<body>
    <div class="main-content">
        <div class="header-container">
            <h1>Ticket Management</h1>
            <a href="login.php" class="logout-container">
                <img src="logout.png" alt="Logout" class="logout-btn">
            </a>
        </div>
        <div class="sidebar">
            <ul>
                <li><a href="admin-indexpage.php">Home </a></li>
                <li><a href="admin-equipmentmanagement.php">Equipment </a></li>
                <li><a href="admin-accountsmanagement.php">Accounts </a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="container">
            <h1>Assign Ticket</h1>
            <form method="POST">
                <label><strong>Ticket Number:</strong> <?php echo htmlspecialchars($ticket['ticketnumber']); ?></label><br>
                <label><strong>Problem:</strong> <?php echo htmlspecialchars($ticket['problem']); ?></label><br>
                <label><strong>Details:</strong> <?php echo htmlspecialchars($ticket['details']); ?></label><br><br>

                <label><strong>Assign to Technician:</strong></label>
                <select name="technician" required>
                    <option value="" disabled selected>Select Technician</option>
                    <?php while ($row = mysqli_fetch_assoc($technical)) { ?>
                        <option value="<?php echo htmlspecialchars($row['username']); ?>">
                            <?php echo htmlspecialchars($row['username']); ?>
                        </option>
                    <?php } ?>
                </select>
                <br><br>

                <div class="button-container">
                    <button type="submit" name="btnsubmit" class="btn btn-update">Save</button>
                    <a href="admin-ticketmanagement.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($link); ?>
