<?php
session_start();

// Redirect if the admin is not logged in
if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php'; // Include database connection

// Enable detailed error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetching salary slip data for all employees
$salaryQuery = "
    SELECT 
        e.id, 
        CONCAT(e.first_name, ' ', e.last_name) AS employee_name, 
        e.monthly_salary, 
        e.net_salary, 
        e.contact_num, 
        e.position, 
        b.branch_name, 
        b.department_manager, 
        b.department_address, 
        COALESCE(SUM(d.amount), 0) AS total_deductions
    FROM employees e
    LEFT JOIN branch_employee be ON e.id = be.employee_id
    LEFT JOIN branch b ON be.branch_id = b.id
    LEFT JOIN deduction d ON e.id = d.employee_id
    GROUP BY e.id
";

$result = $conn->query($salaryQuery);

// Error handling for query execution
if (!$result) {
    die("Query Failed: " . $conn->error);
}

// Fetch employee salary slip details (if employee_id is passed)
if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];
    $salaryDetailsQuery = "
        SELECT 
            e.id, 
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name, 
            e.contact_num, 
            e.position, 
            e.monthly_salary, 
            e.net_salary, 
            b.branch_name, 
            b.department_manager, 
            b.department_address,
            COALESCE(SUM(d.amount), 0) AS total_deductions
        FROM employees e
        LEFT JOIN branch_employee be ON e.id = be.employee_id
        LEFT JOIN branch b ON be.branch_id = b.id
        LEFT JOIN deduction d ON e.id = d.employee_id
        WHERE e.id = ?
        GROUP BY e.id
    ";

    $stmt = $conn->prepare($salaryDetailsQuery);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    // If there's data, return it as JSON
    if ($result->num_rows > 0) {
        $employeeData = $result->fetch_assoc();
        echo json_encode($employeeData);
        exit;
    } else {
        echo json_encode(['error' => 'No data found for this employee']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip</title>
    <link rel="stylesheet" href="./css/salarySlip.css">
    <style>
        /* Modal Styles */
        .salary-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 2;
        }

        .salary-modal-content {
            background-color: #fff;
            padding: 20px;
            width: 600px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .salary-modal-content h2 {
            margin-bottom: 20px;
        }

        .salary-modal-content p {
            margin: 10px 0;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .salary-modal-content button {
            padding: 10px 20px;
            background-color: #ffa07a;
            color: white;
            border: none;
            cursor: pointer;
        }

        .salary-modal-content button:hover {
            background-color: #ff7f50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
        #printable-area {
        border: 1px solid #000;
        padding: 50px;
        margin: 10px 0;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    #printable-area p {
        line-height: 1.5;
    }

    @media print {
        .salary-modal-content {
            box-shadow: none;
            padding: 0;
            width: auto;
        }
        .close-btn, button {
            display: none;
        }
    }
    </style>
</head>
<body>

<!-- header -->
<?php include_once("header.php"); ?>

<!-- siderbar -->
<?php include_once("sidebar.php"); ?>

<main class="content">
    <div class="salary-box">Salary Slip</div>
    <div class="controls">
        <div class="search-container">
            <input type="text" placeholder="Search" class="search-bar">
            <button class="search-btn">🔍</button>
        </div>
    </div>
    <table class="form-container">
        <thead>
            <tr>
                <th>Employee ID</th> <!-- Added Employee ID column -->
                <th>Employee Name</th>
                <th>Monthly Salary</th>
                <th>Total Deductions</th>
                <th>Net Salary</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td> <!-- Display Employee ID -->
                        <td><?= htmlspecialchars($row['employee_name']) ?></td>
                        <td>₱<?= number_format($row['monthly_salary'], 2) ?></td>
                        <td>₱<?= number_format($row['total_deductions'], 2) ?></td>
                        <td>₱<?= number_format($row['net_salary'], 2) ?></td>
                        <td>
                            <button onclick="viewSalarySlip(<?= $row['id'] ?>)" class="view">
                                <img src="eye.png" alt="View" style="height: 20px; width: 30px;">
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No salary data found.</td> <!-- Adjusted colspan for new column -->
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer class="footer">
    © 2024 Payroll Management System. All rights reserved.
</footer>

<div id="salary-modal" class="salary-modal">
    <div class="salary-modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Salary Slip Details</h2>
        <div id="printable-area">
            <p><strong>Employee Name:</strong> <span id="modal-employee-name"></span></p>
            <p><strong>Employee ID:</strong> <span id="modal-employee-id"></span></p>
            <p><strong>Branch:</strong> <span id="modal-branch"></span></p>
            <p><strong>Manager:</strong> <span id="modal-manager"></span></p>
            <p><strong>Address:</strong> <span id="modal-address"></span></p>
            <p><strong>Position:</strong> <span id="modal-position"></span></p>
            <p><strong>Contact Number:</strong> <span id="modal-contact"></span></p>
            <p><strong>Monthly Salary:</strong> <span id="modal-monthly-salary"></span></p>
            <p><strong>Total Deductions:</strong> <span id="modal-total-deductions"></span></p>
            <p><strong>Net Salary:</strong> <span id="modal-net-salary"></span></p>
        </div>
        <button onclick="printSlip()">Print Salary Slip</button>
    </div>
</div>

<script>
function viewSalarySlip(employeeId) {
    console.log("View button clicked for employee ID:", employeeId);  // Confirming button click
    fetch(`salarySlip.php?employee_id=${employeeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            document.getElementById("modal-employee-name").textContent = data.employee_name;
            document.getElementById("modal-employee-id").textContent = data.id;
            document.getElementById("modal-branch").textContent = data.branch_name;
            document.getElementById("modal-manager").textContent = data.department_manager;
            document.getElementById("modal-address").textContent = data.department_address;
            document.getElementById("modal-position").textContent = data.position;
            document.getElementById("modal-contact").textContent = data.contact_num;
            document.getElementById("modal-monthly-salary").textContent = "₱" + data.monthly_salary;
            document.getElementById("modal-total-deductions").textContent = "₱" + data.total_deductions;
            document.getElementById("modal-net-salary").textContent = "₱" + data.net_salary;
            document.getElementById("salary-modal").style.display = "flex";  // Show the modal
        })
        .catch(error => console.error('Error fetching data:', error));
}

function closeModal() {
    document.getElementById("salary-modal").style.display = "none";  // Close the modal
}

function printSlip() {
    const printContents = document.getElementById("printable-area").innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
function showLogoutConfirmation(event) {
            event.preventDefault();
            document.getElementById("logoutOverlay").style.display = "flex";
        }

        function closeLogoutConfirmation() {
            document.getElementById("logoutOverlay").style.display = "none";
        }
        function showLogoutModal() {
            document.getElementById('logoutOverlay').style.display = 'flex';
        }
        function closeLogoutConfirmation() {
            document.getElementById('logoutOverlay').style.display = 'none';
        }
</script>

</body>
</html>
