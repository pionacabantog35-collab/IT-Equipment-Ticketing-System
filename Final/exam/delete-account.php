<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['username'])) {
    $username = trim($_GET['username']);

    // Delete account
    $sql = 'DELETE FROM tblaccounts WHERE username = ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        if (mysqli_stmt_execute($stmt)) {
            
            // Log deletion
            $log_sql = 'INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)';
            if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                $date = date('d/m/Y');
                $time = date('H:i:sa');
                $action = 'Delete';
                $module = 'Accounts Management';
                $performedby = $_SESSION['username'] ?? 'Unknown';

                mysqli_stmt_bind_param($log_stmt, 'ssssss', $date, $time, $action, $module, $username, $performedby);
                mysqli_stmt_execute($log_stmt);
            }

            // Set success message in session
            $_SESSION['success_message'] = "User account '$username' successfully deleted.";

            // Redirect back to accounts management
            header('Location: admin-accountsmanagement.php');
            exit();
        }
    }
}
?>
