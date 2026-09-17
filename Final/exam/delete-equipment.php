<?php
require_once 'config.php';
session_start(); // Ensure session starts

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['assetnumber'])) {
    $assetnumber = trim($_GET['assetnumber']);

    // Delete query
    $sql = 'DELETE FROM tblequipments WHERE assetnumber = ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $assetnumber);
        if (mysqli_stmt_execute($stmt)) {
            // Log deletion
            $log_sql = 'INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)';
            if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                $date = date('d/m/Y');
                $time = date('H:i:sa');
                $action = 'Delete';
                $module = 'Equipment management';
                $performedby = $_SESSION['username'] ?? 'Unknown';

                mysqli_stmt_bind_param($log_stmt, 'ssssss', $date, $time, $action, $module, $assetnumber, $performedby);
                mysqli_stmt_execute($log_stmt);
            }

            // Set session message
            $_SESSION['success_message'] = "Equipment #$assetnumber deleted successfully!";

            // Redirect to the equipment management page
            header('Location: admin-equipmentmanagement.php');
            exit();
        } else {
            $_SESSION['success_message'] = "Error deleting equipment: " . mysqli_error($link);
            header('Location: admin-equipmentmanagement.php');
            exit();
        }
    }
}
?>
