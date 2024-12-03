<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php"); 
    exit;
}
// Connect to the database
include_once("connection.php");

// Add payroll logic
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $dateFrom = $_POST['dateFrom'];
    $dateTo = $_POST['dateTo'];
    $payrollType = $_POST['payrollType'];

    $sql = "INSERT INTO payroll (date_from, date_to, payroll_type) VALUES ('$dateFrom', '$dateTo', '$payrollType')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    exit();
}

// Edit payroll logic
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['payrollId'];
    $dateFrom = $_POST['dateFrom'];
    $dateTo = $_POST['dateTo'];
    $payrollType = $_POST['payrollType'];

    $sql = "UPDATE payroll SET date_from = '$dateFrom', date_to = '$dateTo', payroll_type = '$payrollType' WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    exit();
}

// Delete payroll logic
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['payrollId'];
    $sql = "DELETE FROM payroll WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    exit();
}

// Search query logic
$searchQuery = "WHERE 1";  // Default to showing all records

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $conn->real_escape_string($_GET['search']);
    $searchQuery = "WHERE id LIKE '%$searchTerm%' OR date_from LIKE '%$searchTerm%' OR date_to LIKE '%$searchTerm%' OR payroll_type LIKE '%$searchTerm%'";
}

$sql = "SELECT * FROM payroll $searchQuery ORDER BY payroll_type ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll List</title>
    <link rel="stylesheet" href="./assets/css/payrollList.css">
</head>
<body>

<!-- header -->
<?php include_once("./includes/header.php"); ?>

<!-- sidebar -->
<?php include_once("./includes/sidebar.php"); ?>

    <main class="content">
        <div class="welcome-box2">
            PAYROLL LIST
        </div>

        <div class="controls">
            <div class="search-container">
                <form method="GET" action="payrollList.php">
                    <input type="text" name="search" placeholder="Search ID" class="search-bar" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
            <button class="add-payroll-btn" onclick="showModal()">+ Add Payroll</button>
        </div>

        <table class="payroll-table">
            <thead>
                <tr>
                    <th>Payroll ID</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['date_from'] . "</td>";
                        echo "<td>" . $row['date_to'] . "</td>";
                        echo "<td>" . $row['payroll_type'] . "</td>";
                        echo "<td class='action-buttons'>
                                <button class='edit' onclick='editPayroll(" . $row['id'] . ", \"" . $row['date_from'] . "\", \"" . $row['date_to'] . "\", \"" . $row['payroll_type'] . "\")'>✏️</button>
                                <a href='#' class='delete' onclick='showDeleteConfirmation(" . $row['id'] . ")'><img src='./assets/images/delete.png' alt='delete' style='height: 25px; width: 25px; padding-top: 5px'></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No payroll records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </main>
    
    <!-- footer -->
    <?php include_once("./includes/footer.php"); ?>

    <!-- Modal for Add/Edit Payroll -->
<div id="newPayrollModal" class="modal">
    <div class="modal-content">
        <h2 id="modalHeader">Add Payroll</h2>
        <form id="payrollForm" onsubmit="event.preventDefault(); savePayroll();">
            <div class="modal-body">
                <!-- From Date -->
                <div class="form-group">
                    <label for="dateFrom">From:</label>
                    <input type="date" id="dateFrom" name="dateFrom" required placeholder="Select Start Date">
                </div>

                <!-- To Date -->
                <div class="form-group">
                    <label for="dateTo">To:</label>
                    <input type="date" id="dateTo" name="dateTo" required placeholder="Select End Date">
                </div>

                <!-- Payroll Type -->
                <div class="form-group">
                    <label for="payrollType">Type:</label>
                    <input type="text" id="payrollType" name="payrollType" required placeholder="Enter Payroll Type">
                </div>
            </div>
            
            <!-- Modal Footer with Buttons -->
            <div class="modal-footer">
                <button type="submit" class="save-btn">Save Payroll</button>
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>


    <script>
        let editingPayrollId = null;

        function showModal() {
            document.getElementById('newPayrollModal').style.display = 'flex';
            document.getElementById('modalHeader').textContent = "New Payroll";
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            document.getElementById('payrollType').value = '';
            editingPayrollId = null; // Reset the ID when adding a new payroll
        }

        function closeModal() {
            document.getElementById('newPayrollModal').style.display = 'none';
        }

        function savePayroll() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const payrollType = document.getElementById('payrollType').value;

            const formData = new FormData();
            
            // Check if we're editing or adding
            formData.append('action', editingPayrollId ? 'edit' : 'add');
            
            // If editing, include the payroll ID
            if (editingPayrollId) {
                formData.append('payrollId', editingPayrollId);
            }

            formData.append('dateFrom', dateFrom);
            formData.append('dateTo', dateTo);
            formData.append('payrollType', payrollType);

            fetch('payrollList.php', {
                method: 'POST',
                body: formData,
            }).then(response => response.json())
              .then(data => {
                if (data.success) {
                    window.location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        }

        function editPayroll(id, dateFrom, dateTo, payrollType) {
            document.getElementById('newPayrollModal').style.display = 'flex';
            document.getElementById('modalHeader').textContent = "Edit Payroll";
            document.getElementById('dateFrom').value = dateFrom;
            document.getElementById('dateTo').value = dateTo;
            document.getElementById('payrollType').value = payrollType;
            editingPayrollId = id;
        }

        function showDeleteConfirmation(id) {
            if (confirm("Are you sure you want to delete this payroll?")) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('payrollId', id);

                fetch('payrollList.php', {
                    method: 'POST',
                    body: formData,
                }).then(response => response.json())
                  .then(data => {
                    if (data.success) {
                        window.location.reload(); // Reload to remove deleted payroll
                    } else {
                        alert('Failed to delete payroll: ' + data.message);
                    }
                }).catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>
