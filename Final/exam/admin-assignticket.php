<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'ADMINISTRATOR') {
    header("Location: login.php");
    exit();
}

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
    
    $assign_sql = "UPDATE tbltickets SET assigned_to = ?, status = ?, dateAssigned = ? WHERE ticketnumber = ?";
    if ($stmt = mysqli_prepare($link, $assign_sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $technician, $status, $dateAssigned, $ticketnumber);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: ticket-management.php");
    exit();
}

$technicians_sql = "SELECT username FROM tblusers WHERE usertype = 'TECHNICAL'";
$technical = mysqli_query($link, $technicians_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Ticket</title>
    <link rel="stylesheet" href="update-ticketstyle.css">
</head>
<body>
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
            
            <button type="submit">Save</button>
            <a href="ticket-management.php"><button type="button">Cancel</button></a>
        </form>
    </div>
</body>
</html>

<?php mysqli_close($link); ?>