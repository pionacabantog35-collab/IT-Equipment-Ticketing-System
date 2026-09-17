<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check if ticket number is provided
if (!isset($_GET['ticketnumber'])) {
    header('Location: ticket-management.php');
    exit();
}

$ticketnumber = trim($_GET['ticketnumber']);

// Fetch existing ticket details
$sql = 'SELECT * FROM tbltickets WHERE ticketnumber = ?';
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, 's', $ticketnumber);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $problem = $row['problem'];
            $details = $row['details'];
        } else {
            header('Location: user-ticketmanagement.php');
            exit();
        }
    }
    mysqli_stmt_close($stmt);
}

// Handle form submission for updating ticket
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsubmit'])) {
    $new_problem = trim($_POST['problem']);
    $new_details = trim($_POST['details']);

    $update_sql = 'UPDATE tbltickets SET problem = ?, details = ? WHERE ticketnumber = ?';
    if ($update_stmt = mysqli_prepare($link, $update_sql)) {
        mysqli_stmt_bind_param($update_stmt, 'sss', $new_problem, $new_details, $ticketnumber);
        if (mysqli_stmt_execute($update_stmt)) {
            
            // Log update action
            $log_sql = 'INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)';
            if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                $date = date('d/m/Y');
                $time = date('H:i:sa');
                $action = 'Update';
                $module = 'Ticket Management';
                $performedby = $_SESSION['username'] ?? 'Unknown';
                mysqli_stmt_bind_param($log_stmt, 'ssssss', $date, $time, $action, $module, $ticketnumber, $performedby);
                mysqli_stmt_execute($log_stmt);
            }
            
            $_SESSION['success_message'] = "Ticket #$ticketnumber updated successfully.";
            header('Location: user-ticketmanagement.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Ticket</title>
    <link rel="stylesheet" href="update-ticketstyle.css">
</head>
<body>
    <div class="sidebar">
         <ul>
        <li><a href="user-indexpage.php">Home </a></li>
        <li><a href="user-equipmentmanagement.php">Equipments</a></li>
        <li><a href="user-ticketmanagement.php">Ticket</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
    </div>
    <div class="main-content">
        <div class="header-container">
            <h1>Ticket Management Page</h1>
        </div>
         <div class="container">
        <h1>Update Ticket</h1>
        <form action="" method="POST">
            <label>Ticket Number:</label>
            <input type="text" value="<?php echo htmlspecialchars($ticketnumber); ?>" disabled>
            <label>Problem:</label>
            <select name="problem" required>
                <option value="Hardware" <?php if ($problem == 'Hardware') echo 'selected'; ?>>Hardware</option>
                <option value="Software" <?php if ($problem == 'Software') echo 'selected'; ?>>Software</option>
                <option value="Connection" <?php if ($problem == 'Connection') echo 'selected'; ?>>Connection</option>
            </select>
            <label>Details:</label>
            <textarea name="details" required><?php echo htmlspecialchars($details); ?></textarea>
            <div class="button-container">
    <button type="submit" name="btnsubmit" class="btn btn-update">Save</button>
    <a href="user-ticketmanagement.php" class="btn btn-cancel">Cancel</a>
</div>

    </div>
</body>
</html>
