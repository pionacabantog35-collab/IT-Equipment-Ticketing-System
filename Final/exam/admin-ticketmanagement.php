<?php
session_start();
require_once "config.php";

// Check if the user is logged in and is an administrator
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'ADMINISTRATOR') {
    header("Location: login.php");
    exit();
}

// Handle ticket closure
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticketnumber'])) {
    $ticketNumber = $_POST['ticketnumber'];
    $adminUsername = $_SESSION['username'];
    $approvalDate = date("Y-m-d H:i:s");

    // Update the ticket status to CLOSED in the database
    $sql = "UPDATE tbltickets SET status = 'CLOSED', dateapproved = ?, approvedby = ? WHERE ticketnumber = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $approvalDate, $adminUsername, $ticketNumber);
        if (mysqli_stmt_execute($stmt)) {
            $date = date("d/m/Y");
            $time = date("h:i:sa");
            $action = "Approve";
            $module = "Ticket Management";
            $log_sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                mysqli_stmt_bind_param($log_stmt, "ssssss", $date, $time, $action, $module, $ticketNumber, $adminUsername);
                mysqli_stmt_execute($log_stmt);
                mysqli_stmt_close($log_stmt);
        }
            $_SESSION['success_message'] = "Ticket #$ticketNumber has been closed successfully.";
        } else {
            $_SESSION['error_message'] = "Error closing ticket.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error_message'] = "Database error.";
    }

    mysqli_close($link);
    header("Location: admin-ticketmanagement.php"); // Reload the page after closing ticket
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management</title>
    <link rel="stylesheet" href="managementstyle.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="admin-indexpage.php">Home</a></li>
        <li><a href="admin-equipmentmanagement.php">Equipment</a></li>
        <li><a href="admin-accountsmanagement.php">Accounts</a></li>
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
        <h1>Ticket Management Page</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="search-container">
                <input type="text" id="search" name="txtsearch" placeholder="Search Ticket">
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
    
    <?php
    function buildTable($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr>
                    <th>Ticket Number</th>
                    <th>Problem</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                 </tr>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['ticketnumber']) . "</td>";
                echo "<td>" . htmlspecialchars($row['problem']) . "</td>";
                echo "<td>" . htmlspecialchars(date("Y-m-d", strtotime($row['datecreated']))) . "</td>"; 
                echo "<td>" . htmlspecialchars(date("H:i:s", strtotime($row['datecreated']))) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";

                // Actions
                echo "<td>";
                echo "<div class='button-container'>"; 

                // Details - always enabled
                echo "<button class='btn btn-details' onclick='showDetails(" . json_encode($row) . ")'>Details</button>";

                // Assign - enabled only for PENDING or ON-GOING
               $assignDisabled = (!in_array($row['status'], array('PENDING', 'ON-GOING'))) ? 'disabled' : '';
echo "<button class='btn btn-assign' onclick='location.href=\"assign.php?ticketnumber=" . urlencode($row['ticketnumber']) . "\"' $assignDisabled>Assign</button>";


                // Approve - enabled only for WAITING FOR APPROVAL
                $approveDisabled = ($row['status'] != 'WAITING FOR APPROVAL') ? 'disabled' : '';
                echo "<button class='btn btn-approve' onclick='approveTicket(\"" . $row['ticketnumber'] . "\")' $approveDisabled>Approve</button>";

                // Delete - enabled only if CLOSED
                $deleteDisabled = ($row['status'] != 'CLOSED') ? 'disabled' : '';
                echo "<button class='btn btn-cancel' onclick='deleteTicket(\"" . $row['ticketnumber'] . "\")' $deleteDisabled>Delete</button>";

                echo "</div>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No records found.</p>";
        }
    }

    $searchQuery = "%";
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsearch'])) {
        $searchQuery = "%" . $_POST['txtsearch'] . "%";
    }

    $sql = "SELECT * FROM tbltickets WHERE ticketnumber LIKE ? OR problem LIKE ? OR status LIKE ? ORDER BY datecreated DESC";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $searchQuery, $searchQuery, $searchQuery);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            buildTable($result);
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
    ?>
</div>
    <footer class="footer">
  <p>&copy; 2025 Piona Cabantog. All rights reserved.</p>
</footer>
</div>


<!-- Modal Sections -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Ticket Details</h2>
        <div id="modal-body"></div>
    </div>
</div>

<div id="approveModal" class="modal">
    <div class="modal-content">
        <h2>Approve Ticket</h2>
        <p>Are you sure you want to approve this ticket?</p>
        <form id="approveForm" method="POST" action="admin-ticketmanagement.php">
            <input type="hidden" name="ticketnumber" id="approveTicketNumber">
       
        <div class="button-container1">
            <button type="submit" name="btnsubmit" class="btn btn-approve1">Approve</button>
            <a href="#" onclick="closeApproveModal()" class="btn btn-cancel">Cancel</a>
        </div>

        </form>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h2>Delete Ticket</h2>
        <p>Are you sure you want to delete this ticket?</p>
        <form id="deleteForm" method="GET" action="admin-deleteticket.php">
            <input type="hidden" name="ticketnumber" id="deleteTicketNumber">
            
<div class="button-container1">
    <button type="submit" name="btnsubmit" class="btn btn-delete">Delete</button>
    <a href="#" onclick="closeDeleteModal()" class="btn btn-cancel">Cancel</a>
</div>

    </div>
</div>

<script>
// Show details modal
function showDetails(ticket) {
    let modalBody = document.getElementById("modal-body");
    modalBody.innerHTML = ""; // Clear the modal body content

    // Loop through the ticket and show each property
    for (let key in ticket) {
        let formattedKey = key.replace(/_/g, " ");  // Format the ticket keys
        modalBody.innerHTML += `<p><strong>${formattedKey}:</strong> ${ticket[key]}</p>`;
    }
    document.getElementById("detailsModal").style.display = "flex";  // Show modal when button is clicked
}

// Close details modal
function closeModal() {
    document.getElementById("detailsModal").style.display = "none";  // Hide modal when clicked
}

// Approve modal show
function approveTicket(ticketNumber) {
    document.getElementById("approveTicketNumber").value = ticketNumber;
    document.getElementById("approveModal").style.display = "flex";  // Show approve modal when clicked
}

// Close approve modal
function closeApproveModal() {
    document.getElementById("approveModal").style.display = "none";  // Hide approve modal
}

// Delete ticket modal show
function deleteTicket(ticketNumber) {
    document.getElementById("deleteTicketNumber").value = ticketNumber;
    document.getElementById("deleteModal").style.display = "flex";  // Show delete modal when clicked
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById("deleteModal").style.display = "none";  // Hide delete modal
}
</script>

</body>
</html>
