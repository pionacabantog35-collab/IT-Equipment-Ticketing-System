<?php
session_start();
require_once "config.php";

// Ensure only technical users can access
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'TECHNICAL') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$searchQuery = "%";

// Handle search query
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsearch'])) {
    if (!empty($_POST['txtsearch'])) {
        $searchQuery = "%" . $_POST['txtsearch'] . "%";
    }
}

// Fetch assigned tickets
$sql = "SELECT * FROM tbltickets WHERE assignedto = ? AND (ticketnumber LIKE ? OR problem LIKE ? OR status LIKE ?) ORDER BY datecreated DESC";
$result = null;
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ssss", $username, $searchQuery, $searchQuery, $searchQuery);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Ticket Management</title>
    <link rel="stylesheet" href="managementstyle.css">
</head>
<body>
<div class="sidebar">
    <ul>
        <li><a href="tech-indexpage.php">Home</a></li>
         <li><a href="tech-equipmentmanagement.php">Equipments</a></li>

        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
 <div class="page-content">

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
                $ticketNumber = htmlspecialchars($row['ticketnumber']);
                $status = trim(strtoupper($row['status'])); // Normalize status

                echo "<tr>";
                echo "<td>" . $ticketNumber . "</td>";
                echo "<td>" . htmlspecialchars($row['problem']) . "</td>";
                echo "<td>" . htmlspecialchars(date("Y-m-d", strtotime($row['datecreated']))) . "</td>"; 
                echo "<td>" . htmlspecialchars(date("H:i:s", strtotime($row['datecreated']))) . "</td>";
                echo "<td>" . $status . "</td>";

                echo "<td>";
                echo "<div class='button-container'>";
                 echo "<button class='btn btn-details' onclick='showDetails(" . json_encode($row) . ")'>Details</button>";

                // Only enable the button if the status is "ONGOING"
                if ($status === 'ON-GOING') {
                    echo "<button class='btn btn-complete' onclick='completeTicket(\"$ticketNumber\")'>Complete</button>";
                } else {
                    echo "<button class='btn btn-complete' disabled>Complete</button>";
                }

                echo "</div>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p class='no-records'>No records found.</p>";
        }
    }

    // Call the function to display the table
    buildTable($result);
    ?>

</div>
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Ticket Details</h2>
        <div id="modal-body"></div>
    </div>
</div>
<!-- Complete Ticket Modal -->
<div id="completeModal" class="modal">
    <div class="modal-content">
        <h2>Complete Ticket</h2>
        <p>Are you sure you want to mark this ticket as completed?</p>
        <form id="completeForm" method="POST" action="complete-ticket.php">
            <input type="hidden" name="ticketnumber" id="completeTicketNumber">

            <div class="button-container1">
            <button type="submit" class="btn btn-complete">Complete</button>
            <button type="button" onclick="closeCompleteModal()"class="btn btn-cancel">Cancel</button>
        </div>
        </form>
    </div>
    </div>
</div>
<footer class="footer">
  <p>&copy; 2025 Piona Cabantog. All rights reserved.</p>
</footer>
</div>
<script>
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
function completeTicket(ticketNumber) {
    document.getElementById("completeTicketNumber").value = ticketNumber;
    document.getElementById("completeModal").style.display = "flex";
}

function closeCompleteModal() {
    document.getElementById("completeModal").style.display = "none";
}
</script>

</body>
</html>
