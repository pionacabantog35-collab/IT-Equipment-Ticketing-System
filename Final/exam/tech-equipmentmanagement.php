<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management Page - AU Technical Support Management System</title>
    <link rel="stylesheet" href= "managementstyle.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="tech-indexpage.php">Home </a></li>
            <li><a href="tech-ticketmanagement.php">Ticket </a></li>
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
            <h1>Equipment Management Page</h1>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="search-container">
                    <input type="text" id="search" name="txtsearch" placeholder="Search equipment">
                    <input type="submit" name="btnsearch" value="Search">
                </div>
            </form>
        </div>

        <!-- Popup Message -->
        <div id="popup-message" class="popup" style="display: none;">
            <p id="popup-text"></p>
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
            <a href="tech-addequipment.php" class="btn btn-add">Add Equipment</a>
        </div>

        <?php
        require_once "config.php";

        function buildTable($result) {
            if (mysqli_num_rows($result) > 0) {
                echo "<table>";
                echo "<tr>
                    <th>Asset Number</th>
                    <th>Serial Number</th>
                    <th>Type</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['assetnumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['serialnumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['branch']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['createdby']) . "</td>";
                    
                    echo "<td>";

                    echo "<div class='button-container'>";
                    echo "<a href='tech-updateequipment.php?assetnumber=" . urlencode($row['assetnumber']) . "' class='btn btn-update'>Update</a>";

            echo "<a href='#' class='btn btn-cancel' onclick='openDeleteModal(\"" . htmlspecialchars($row['assetnumber']) . "\")'>Delete</a>";
    
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No records found.</p>";
            }
        }

        // Handle Search Query
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsearch'])) {
            $searchValue = '%' . $_POST['txtsearch'] . '%';
            $sql = "SELECT * FROM tblequipments 
            WHERE assetnumber LIKE ? OR serialnumber LIKE ? OR type LIKE ? 
            OR branch LIKE ? OR status LIKE ? OR createdby LIKE ? 
            ORDER BY datecreated DESC, assetnumber DESC";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssss", $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    buildTable($result);
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $sql = "SELECT * FROM tblequipments ORDER BY datecreated DESC, assetnumber DESC";
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
                <h2>Delete Equipment</h2>
                <p>Are you sure you want to delete this equipment?</p>
                <form id="deleteForm" method="GET" action="tech-deleteequipment.php">
                    <input type="hidden" name="assetnumber" id="deleteAssetNumber">
                    
                    <!-- Button wrapper for layout -->
                    <div class="modal-buttons">
                        <button type="submit" class="btn btn-delete">Delete</button>
                        <button type="button" onclick="closeDeleteModal()" class="btn btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <footer class="footer">
  <p>&copy; 2025 Piona Cabantog. All rights reserved.</p>
</footer>
    <script>
        function showPopup(message) {
            var popup = document.getElementById('popup-message');
            var popupText = document.getElementById('popup-text');
            popupText.textContent = message;
            popup.style.display = 'block';

            setTimeout(function() {
                popup.style.display = 'none';
            }, 3000);
        }

        // Show session message if exists
        <?php if (isset($_SESSION['success_message'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup("<?php echo addslashes($_SESSION['success_message']); ?>");
            });
            <?php unset($_SESSION['succes_message']); ?>
        <?php endif; ?>

    </script>
     <script>
function openDeleteModal(assetNumber) {
    document.getElementById('deleteAssetNumber').value = assetNumber;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

</script>

</body>
</html>
