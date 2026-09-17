<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts Management - AU Technical Support Management System</title>
    <link rel="stylesheet" href="managementstyle.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="admin-indexpage.php">Home </a></li>
        <li><a href="admin-equipmentmanagement.php">Equipments </a></li>
        <li><a href="admin-ticketmanagement.php">Tickets</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
 <div class="page-content">
<!-- Main Content -->
<div class="main-content">
    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
        unset($_SESSION['success_message']); 
    }
    ?>
    
    <div class="header-container">
        <h1>Accounts Management Page</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="search-container">
                <input type="text" id="search" name="txtsearch" placeholder="Search Account">
                <input type="submit" name="btnsearch" value="Search">
            </div>
        </form>
    </div>
<div class="welcome-container">
     <img src="users.png" alt="User Image" class="user-image">
    <h2 class="welcome-message">
        Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
    </h2>
    <h3 class="account-type">
        Account type: <?php echo isset($_SESSION['usertype']) ? htmlspecialchars($_SESSION['usertype']) : 'Not Available'; ?>
    </h3>
</div>


    <div class="add-container">
        <a href="create-account.php" class="btn btn-add">Create New Account</a>
    </div>

    <?php
    function buildTable($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr>
                <th>Username</th>
                <th>Usertype</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['usertype']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['createdby']) . "</td>";
             echo "<td>" . date("d-m-Y", strtotime($row['datecreated'])) . "</td>";

                echo "<td>";
                echo "<div class='button-container'>";
                echo "<a href='update-account.php?username=" . urlencode($row['username']) . "' class='btn btn-update'>Update</a>";

                  echo "<a href='#' class='btn btn-cancel' onclick='openDeleteModal(\"" . htmlspecialchars($row['username']) . "\")'>Delete</a>";
               
                echo "</div>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found.</p>";
        }
    }

    // Search functionality
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsearch'])) {
        $searchValue = '%' . $_POST['txtsearch'] . '%';
      $sql = "SELECT * FROM tblaccounts WHERE username LIKE ? OR usertype LIKE ? ORDER BY username ASC";


        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $searchValue, $searchValue);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                buildTable($result);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
     $sql = "SELECT * FROM tblaccounts ORDER BY username ASC";

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                buildTable($result);
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
    ?>
             <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="modal" style="display:none;">
            <div class="modal-content">
                <h2>Delete Account</h2>
                <p>Are you sure you want to delete this account?</p>
                <form id="deleteForm" method="GET" action="delete-account.php">
                    <input type="hidden" name="username" id="deleteaccount">
                    
                    <!-- Button wrapper for layout -->
                    <div class="modal-buttons">
                        <button type="submit" class="btn btn-delete">Delete</button>
                        <button type="button" onclick="closeDeleteModal()" class="btn btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
</div>
</div>
<footer class="footer">
  <p>&copy; 2025 Piona Cabantog. All rights reserved.</p>
</footer>


<script >
    function openDeleteModal(username) {
    document.getElementById('deleteaccount').value = username;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>


</body>
</html>