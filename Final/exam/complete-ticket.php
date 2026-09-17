<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticketnumber'])) {
    $ticketNumber = $_POST['ticketnumber'];
    
    // Get the current date and time
    $dateCompleted = date("Y-m-d H:i:s");

    // Update the ticket status to "WAITING FOR APPROVAL" and set dateCompleted
    $sql = "UPDATE tbltickets SET status = 'WAITING FOR APPROVAL', dateCompleted = ? WHERE ticketnumber = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $dateCompleted, $ticketNumber);
        if (mysqli_stmt_execute($stmt)) {
            
            // Log the action in tbllogs
            $action = "Complete";
            $module = "Ticket Management";
            $performedBy = $_SESSION['username'];
            $performedTo = $ticketNumber;
            $date = date("d/m/Y");
            $time = date("H:i:sa");

            // Insert into the logs table
            $logSql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            if ($logStmt = mysqli_prepare($link, $logSql)) {
                mysqli_stmt_bind_param($logStmt, "ssssss", $date, $time, $action, $module, $performedTo, $performedBy);
                mysqli_stmt_execute($logStmt);
                mysqli_stmt_close($logStmt);
            }
            
            $_SESSION['success_message'] = "Ticket $ticketNumber marked as WAITING FOR APPROVAL.";
        } else {
            $_SESSION['error_message'] = "Error updating ticket.";
        }
        mysqli_stmt_close($stmt);
    }

    // Redirect back to the technical ticket management page
    header("Location: tech-ticketmanagement.php");
    exit();
}
?>
