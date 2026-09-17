<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['ticketnumber'])) {
    $ticketnumber = trim($_GET['ticketnumber']);

    // Delete ticket
    $sql = 'DELETE FROM tbltickets WHERE ticketnumber = ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $ticketnumber);
        if (mysqli_stmt_execute($stmt)) {
            // Log deletion
            $log_sql = 'INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)';
            if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                $date = date('d/m/Y');
                $time = date('H:i:sa');
                $action = 'Delete';
                $module = 'Ticket Management';
                $performedby = $_SESSION['username'] ?? 'Unknown';

                mysqli_stmt_bind_param($log_stmt, 'ssssss', $date, $time, $action, $module, $ticketnumber, $performedby);
                mysqli_stmt_execute($log_stmt);
            }

            // Set success message in session
            $_SESSION['success_message'] = "Ticket #$ticketnumber successfully deleted.";

            // Redirect back to ticket management
            header('Location: admin-ticketmanagement.php');
            exit();
        }
    }
}
?>