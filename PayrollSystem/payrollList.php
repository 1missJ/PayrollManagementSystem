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
    $newPayrollDate = $_POST['payroll_date'];
    $stmt = $conn->prepare("INSERT INTO payroll (date) VALUES (?)");
    $stmt->bind_param("s", $newPayrollDate);
    if ($stmt->execute()) {
        echo "<script>alert('Payroll date added successfully'); window.location.href='payrollList.php';</script>";
    } else {
        echo "<script>alert('Error adding payroll date');</script>";
    }
}

// Handle updating a payroll record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_payroll'])) {
    $payrollId = $_POST['payroll_id'];
    $payrollDate = $_POST['payroll_date'];

    // Update payroll record in the database
    $stmt = $conn->prepare("UPDATE payroll SET date = ? WHERE id = ?");
    $stmt->bind_param("si", $payrollDate, $payrollId);

    if ($stmt->execute()) {
        echo "<script>alert('Payroll record updated successfully'); window.location.href='payrollList.php';</script>";
    } else {
        echo "<script>alert('Error updating payroll record');</script>";
    }
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

// Query to fetch payroll data
$sql = "
    SELECT p.id, p.date, 
           CONCAT(e.first_name, ' ', e.last_name) AS employee_name, e.position,
           d.deduction_name, d.amount
    FROM payroll p
    LEFT JOIN employees e ON e.id = p.employee_id
    LEFT JOIN deduction d ON d.id = p.id
    WHERE MONTH(p.date) = ? AND YEAR(p.date) = ?
    ORDER BY p.date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
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
            width: 300px;
            text-align: center;
        }

        .modal-content h2 {
            margin-bottom: 20px;
        }

        .modal-content input {
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
                <th>Payroll Date</th>
                <th>Deductions</th>
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
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['deduction_name'] . ' - ' . $row['amount']) ?></td>
                        <td class="action-buttons">
                            <button class="edit" onclick="editPayroll(<?= $row['id'] ?>)">✏️</button>
                            <a href="?delete_id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this record?')">❌</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No payroll records found for the selected date range.</td>
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
            <input type="date" name="payroll_date" required>
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
            <label for="edit_payroll_date">Payroll Date</label>
            <input type="date" name="payroll_date" id="edit_payroll_date" required>
            <button type="submit" name="edit_payroll">Update</button>
            <button type="button" class="close-btn" onclick="closeEditModal()">Close</button>
        </form>
    </div>
</div>

<!-- footer -->
<?php include_once("./includes/footer.php"); ?>

<script>
    function showModal() {
        document.getElementById('addPayrollModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('addPayrollModal').style.display = 'none';
    }

    function editPayroll(id) {
        // Pre-fill the form for editing
        document.getElementById('edit_payroll_id').value = id;

        // You may fetch payroll date dynamically with AJAX here based on `id`
        document.getElementById('edit_payroll_date').value = '2024-12-01'; // Example value for now

        document.getElementById('editPayrollModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editPayrollModal').style.display = 'none';
    }
</script>

</body>
</html>
