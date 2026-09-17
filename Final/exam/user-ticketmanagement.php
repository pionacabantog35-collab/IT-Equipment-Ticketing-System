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
    <title>Ticket Management Page - AU Technical Support Management System</title>
    <link rel="stylesheet" href="managementstyle.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="user-indexpage.php">Home </a></li>
        <li><a href="user-equipmentmanagement.php">Equipment </a></li>
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

    <div class="add-container">
        <a href="user-createticket.php" class="btn btn-add">Add Ticket</a>
    </div>

    <?php
    function buildTable($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr>
                <th>Ticket Number</th>
                <th>Problem</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
             </tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['ticketnumber']) . "</td>";
                echo "<td>" . htmlspecialchars($row['problem']) . "</td>";
                echo "<td>" . htmlspecialchars($row['datecreated']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>";
                echo "<div class='button-container'>"; 
                echo "<a href='update-ticket.php?ticketnumber=" . urlencode($row['ticketnumber']) . "' class='btn btn-update'>Update</a>";
              // Only show Delete if status is Pending or Closed
          $status = strtolower($row['status']);
$disabled = (!in_array($status, ['pending', 'closed'])) ? 'disabled style="background-color: gray; cursor: not-allowed;"' : '';
echo "<button class='btn btn-cancel' onclick='showDeleteModal(\"" . htmlspecialchars($row['ticketnumber']) . "\")' $disabled>Delete</button>";



                $ticketData = htmlspecialchars(json_encode($row, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
                echo "<button class='btn btn-details' onclick='showDetails($ticketData)'>Details</button>";
                echo "</div>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found.</p>";
        }
    }

    // Search functionality: If the search button is clicked
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsearch'])) {
        $searchValue = '%' . $_POST['txtsearch'] . '%';
        $sql = "SELECT * FROM tbltickets WHERE createdby = ? AND (ticketnumber LIKE ? OR problem LIKE ? OR status LIKE ? OR datecreated LIKE ?) ORDER BY datecreated DESC, ticketnumber DESC";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $_SESSION['username'], $searchValue, $searchValue, $searchValue, $searchValue);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                buildTable($result);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        // Default query to display all tickets created by the logged-in user
        $sql = "SELECT * FROM tbltickets WHERE createdby = ? ORDER BY datecreated DESC, ticketnumber DESC";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                buildTable($result);
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
    ?>


<div id="detailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Ticket Details</h2>
        <div id="modal-body"></div>
    </div>
</div>
<!-- Delete Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2>Delete Ticket</h2>
        <p>Are you sure you want to delete this ticket?</p>
        <form id="deleteForm" method="GET" action="delete-ticket.php">
            <input type="hidden" name="ticketnumber" id="deleteTicketNumber">
            <div class="button-group">
                <button type="submit" class="btn btn-cancel">Delete</button>
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

</div>
<footer class="footer">
  <p>&copy; 2025 Piona Cabantog. All rights reserved.</p>
</footer>


</div>
</div>

<script>
function showDetails(ticket) {
    let modalBody = document.getElementById("modal-body");
    modalBody.innerHTML = "";

    for (let key in ticket) {
        let formattedKey = key.replace(/_/g, " ");
        modalBody.innerHTML += `<p><strong>${formattedKey}:</strong> ${ticket[key]}</p>`;
    }
    document.getElementById("detailsModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("detailsModal").style.display = "none";
}

function showDeleteModal(ticketNumber) {
    document.getElementById("deleteTicketNumber").value = ticketNumber;
    document.getElementById("deleteModal").style.display = "flex";
}

function closeDeleteModal() {
    document.getElementById("deleteModal").style.display = "none";
}
</script>


</body>
</html>