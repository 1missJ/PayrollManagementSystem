<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

// Connect to the database
include_once("connection.php");

// Default to current month and year if not set
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Handle adding a new payroll date
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payroll_date'])) {
    $newDateFrom = $_POST['date_from'];
    $newDateTo = $_POST['date_to'];
    $employeeIds = $_POST['employee_ids'];  // Now it's an array of employee IDs

    // Insert payroll record for all selected employees with the same payroll date range
    foreach ($employeeIds as $employeeId) {
        $stmt = $conn->prepare("INSERT INTO payroll (date_from, date_to, employee_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $newDateFrom, $newDateTo, $employeeId); // Insert the date range
        $stmt->execute();  // Execute the insert for each employee
    }

    echo "<script>alert('Payroll date range added successfully'); window.location.href='payrollList.php';</script>";
}

// Handle updating a payroll record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_payroll'])) {
    $payrollId = $_POST['payroll_id'];
    $dateFrom = $_POST['date_from'];
    $dateTo = $_POST['date_to'];
    $employeeIds = $_POST['employee_ids'];  // Array of employee IDs

    // Delete previous associations and insert the new ones with the same payroll ID
    $stmt = $conn->prepare("DELETE FROM payroll WHERE id = ?");
    $stmt->bind_param("i", $payrollId);
    $stmt->execute();  // Remove previous payroll associations

    // Insert new payroll records for selected employees
    foreach ($employeeIds as $employeeId) {
        $stmt = $conn->prepare("INSERT INTO payroll (id, date_from, date_to, employee_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $payrollId, $dateFrom, $dateTo, $employeeId);
        $stmt->execute();
    }

    echo "<script>alert('Payroll record updated successfully'); window.location.href='payrollList.php';</script>";
}

// Handle deleting a payroll record
if (isset($_GET['delete_id'])) {
    $payrollId = $_GET['delete_id'];

    // Delete the payroll record from the database
    $stmt = $conn->prepare("DELETE FROM payroll WHERE id = ?");
    $stmt->bind_param("i", $payrollId);

    if ($stmt->execute()) {
        echo "<script>alert('Payroll record deleted successfully'); window.location.href='payrollList.php';</script>";
    } else {
        echo "<script>alert('Error deleting payroll record');</script>";
    }
}

// Query to fetch payroll data (Updated to use 'date_from' and 'date_to' instead of 'date')
$sql = "
    SELECT p.id, p.date_from, p.date_to, 
           CONCAT(e.first_name, ' ', e.last_name) AS employee_name, e.position,
           d.deduction_name, d.amount
    FROM payroll p
    LEFT JOIN employees e ON e.id = p.employee_id
    LEFT JOIN deduction d ON d.payroll_id = p.id  -- Assuming 'payroll_id' is used in the 'deduction' table
    WHERE MONTH(p.date_from) = ? AND YEAR(p.date_from) = ?
    ORDER BY p.date_from DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all employees for the dropdown
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';  // Search term
$searchSql = "SELECT id, first_name, last_name FROM employees WHERE CONCAT(first_name, ' ', last_name) LIKE ?";
$searchStmt = $conn->prepare($searchSql);
$searchTerm = '%' . $searchTerm . '%';
$searchStmt->bind_param("s", $searchTerm);
$searchStmt->execute();
$employeesResult = $searchStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll List</title>
    <link rel="stylesheet" href="./assets/css/payrollList.css">
    <style>
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }

        .modal-content h2 {
            margin-bottom: 20px;
        }

        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modal-content button {
            padding: 10px 20px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content .close-btn {
            background: #dc3545;
        }

        /* Employee checkbox layout */
        .checkbox-group {
            max-height: 300px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .checkbox-group input {
            margin-right: 10px;
        }

        /* Style for select all checkbox */
        #selectAll {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
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
                <label for="month">Month:</label>
                <select name="month" id="month">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $month == $m ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <label for="year">Year:</label>
                <input type="number" name="year" id="year" value="<?= $year ?>" required>

                <button type="submit">Filter</button>
            </form>
        </div>

        <button onclick="showModal()">Add Payroll Date</button>
    </div>

    <table class="payroll-table">
        <thead>
            <tr>
                <th>Payroll ID</th>
                <th>Employee Name</th>
                <th>Position</th>
                <th>Date From</th>
                <th>Date To</th>
                
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['employee_name']) ?></td>
                        <td><?= htmlspecialchars($row['position']) ?></td>
                        <td><?= htmlspecialchars($row['date_from']) ?></td>
                        <td><?= htmlspecialchars($row['date_to']) ?></td>
                        
                        <td class="action-buttons">
                            <button class="edit" onclick="editPayroll(<?= $row['id'] ?>)">✏️</button>
                            <a href="?delete_id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this record?')">❌</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No payroll records found for the selected date range.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<!-- Add Payroll Date Modal -->
<div id="addPayrollModal" class="modal">
    <div class="modal-content">
        <h2>Add Payroll Date</h2>
        <form method="POST" action="payrollList.php">
            <label for="date_from">Date From</label>
            <input type="date" name="date_from" required>

            <label for="date_to">Date To</label>
            <input type="date" name="date_to" required>
            
            <!-- Employee Search and Selection -->
            <input type="text" id="employeeSearch" placeholder="Search employees..." onkeyup="searchEmployees()">
            
            <div class="checkbox-group" id="employeeList">
                <?php while ($employee = $employeesResult->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="employee_ids[]" value="<?= $employee['id'] ?>"> 
                        <?= $employee['first_name'] . ' ' . $employee['last_name'] ?>
                    </label>
                <?php endwhile; ?>
            </div>

            <!-- Select All Checkbox -->
            <label>
                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"> 
                Select All
            </label>

            <button type="submit" name="add_payroll_date">Add</button>
            <button type="button" class="close-btn" onclick="closeModal()">Close</button>
        </form>
    </div>
</div>

<!-- Edit Payroll Modal -->
<div id="editPayrollModal" class="modal">
    <div class="modal-content">
        <h2>Edit Payroll Record</h2>
        <form method="POST" action="payrollList.php">
            <input type="hidden" name="payroll_id" id="edit_payroll_id">
            <label for="edit_date_from">Date From</label>
            <input type="date" name="date_from" id="edit_date_from" required>

            <label for="edit_date_to">Date To</label>
            <input type="date" name="date_to" id="edit_date_to" required>

            <!-- Employee Selection with checkboxes -->
            <div class="checkbox-group" id="edit_employeeList">
                <?php while ($employee = $employeesResult->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="employee_ids[]" value="<?= $employee['id'] ?>"> 
                        <?= $employee['first_name'] . ' ' . $employee['last_name'] ?>
                    </label>
                <?php endwhile; ?>
            </div>

            <button type="submit" name="edit_payroll">Update</button>
            <button type="button" class="close-btn" onclick="closeEditModal()">Close</button>
        </form>
    </div>
</div>

<!-- footer -->
<?php include_once("./includes/footer.php"); ?>

<script>
    // Search employees by name
    function searchEmployees() {
        let input = document.getElementById('employeeSearch');
        let filter = input.value.toLowerCase();
        let employeeList = document.getElementById('employeeList');
        let checkboxes = employeeList.getElementsByTagName('label');
        
        for (let i = 0; i < checkboxes.length; i++) {
            let text = checkboxes[i].textContent || checkboxes[i].innerText;
            checkboxes[i].style.display = text.toLowerCase().includes(filter) ? '' : 'none';
        }
    }

    // Toggle select all checkboxes
    function toggleSelectAll() {
        let selectAllCheckbox = document.getElementById('selectAll');
        let checkboxes = document.getElementsByName('employee_ids[]');
        
        for (let checkbox of checkboxes) {
            checkbox.checked = selectAllCheckbox.checked;
        }
    }

    function showModal() {
        document.getElementById('addPayrollModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('addPayrollModal').style.display = 'none';
    }

    function editPayroll(id) {
        // Pre-fill the form for editing
        document.getElementById('edit_payroll_id').value = id;

        // Populate the employee checkboxes (you may want to fetch employee info dynamically)
        document.getElementById('editPayrollModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editPayrollModal').style.display = 'none';
    }
</script>

</body>
</html>
